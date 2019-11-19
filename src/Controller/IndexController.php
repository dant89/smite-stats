<?php

namespace App\Controller;

use App\Service\Smite;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
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
     * @Route("/", name="homepage")
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(): Response
    {
        $gods = $this->smite->getGodsFormatted();
        $gods = array_slice($gods, 1, 12, true);

        $duelRankedLeaderboard = $this->smite->getLeagueLeaderboard(440, 27, 6);
        $joustRankedLeaderboard = $this->smite->getLeagueLeaderboard(450, 27, 6);
        $conquestRankedLeaderboard = $this->smite->getLeagueLeaderboard(451, 27, 6);

        $duelRankedLeaderboard = array_slice($duelRankedLeaderboard,  0, 5, true);
        $joustRankedLeaderboard = array_slice($joustRankedLeaderboard,  0, 5, true);
        $conquestRankedLeaderboard = array_slice($conquestRankedLeaderboard,  0, 5, true);

        $leaderboardTypes = [
            'Conquest' => $conquestRankedLeaderboard,
            'Duel' => $duelRankedLeaderboard,
            'Joust' => $joustRankedLeaderboard
        ];

        return $this->render('index/index.html.twig', [
            'gods' => $gods,
            'leaderboards' => $leaderboardTypes
        ]);
    }
}
