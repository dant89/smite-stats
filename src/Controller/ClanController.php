<?php

namespace App\Controller;

use App\Service\Smite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class ClanController extends AbstractController
{
    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(Smite $smite)
    {
        $this->smite = $smite;
    }

    /**
     * @Route("/clan/{name}", name="clan_view")
     * @param string $name
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(string $name): Response
    {
        $searchResult = $this->smite->searchTeams($name);
        if (is_null($searchResult['TeamId'])) {
            throw new NotFoundHttpException();
        }

        $id = $searchResult['TeamId'];

        $gods = $this->smite->getGodsByNameKey();
        $team = $this->smite->getTeamDetails($id);
        $teamPlayers = $this->smite->getTeamPlayers($id);

        foreach ($teamPlayers as &$teamPlayer) {
            if (!empty($teamPlayer['Name'])) {
                $playerId = $this->smite->getPlayerIdByName($teamPlayer['Name']);
                $teamPlayer['Player_info'] = $this->smite->getPlayerDetailsByPortalId($playerId['player_id']);

                $playerGods = $this->smite->getPlayerGodDetails($teamPlayer['Player_info']['Id']);

                $playerStats = [
                    'Kills' => 0,
                    'Assists' => 0,
                    'Deaths' => 0,
                ];

                foreach ($playerGods as $playerGod) {
                    $playerStats['Kills'] += $playerGod['Kills'];
                    $playerStats['Assists'] += $playerGod['Assists'];
                    $playerStats['Deaths'] += $playerGod['Deaths'];
                }

                $teamPlayer['God_info'] = array_slice($playerGods, 0, 4, true);
                $teamPlayer['Stats_info'] = $playerStats;
            }
        }

        return $this->render('clan/clan.html.twig', [
            'clan' => $team,
            'clan_players' => $teamPlayers,
            'gods' => $gods,
        ]);
    }
}
