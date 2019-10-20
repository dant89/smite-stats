<?php

namespace App\Controller;

use App\Service\Smite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @var Smite
     */
    protected $smite;

    /**
     * IndexController constructor.
     * @param Smite $smite
     */
    public function __construct(Smite $smite)
    {
        $this->smite = $smite;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $gods = $this->smite->getGodsFormatted();
        $team = $this->smite->getTeamDetails();
        $teamPlayers = $this->smite->getTeamPlayers();

        foreach ($teamPlayers as &$teamPlayer) {
            $teamPlayer['Player_info'] = $this->smite->getPlayerDetailsByGamertag($teamPlayer['Name']);
            $playerGods = $this->smite->getPlayerGodDetails($teamPlayer['Player_info']['Id']);
            $teamPlayer['God_info'] = array_slice($playerGods, 0, 5, true);
        }

        return $this->render('index/index.html.twig', [
            'clan' => $team,
            'clan_players' => $teamPlayers,
            'gods' => $gods,
        ]);
    }
}
