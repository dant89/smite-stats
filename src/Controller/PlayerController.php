<?php

namespace App\Controller;

use App\Service\Smite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class PlayerController extends AbstractController
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
     * @Route("/player/{gamertag}", name="player_view")
     * @param string $gamertag
     * @return Response
     * @throws InvalidArgumentException
     */
    public function player(string $gamertag): Response
    {
        $gods = $this->smite->getGodsFormatted();
        $player = $this->smite->getPlayerDetailsByGamertag($gamertag);

        if (empty($player)) {
            throw new NotFoundHttpException();
        }

        $playerGods = $this->smite->getPlayerGodDetails($player['Id']);

        $playerStats = [
            'Kills' => 0,
            'Assists' => 0,
            'Deaths' => 0,
        ];

        foreach($playerGods as $playerGod) {
            $playerStats['Kills'] += $playerGod['Kills'];
            $playerStats['Assists'] += $playerGod['Assists'];
            $playerStats['Deaths'] += $playerGod['Deaths'];
        }

        $player['God_info'] = array_slice($playerGods, 0, 5, true);
        $player['Stats_info'] = $playerStats;

        return $this->render('player/player.html.twig', [
            'player' => $player,
            'gods' => $gods,
        ]);
    }
}
