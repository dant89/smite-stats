<?php

namespace App\Controller;

use App\Entity\ApiCall;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager ,Smite $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
    }

    /**
     * @Route("/", name="homepage")
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(): Response
    {
        $gods = $this->smite->getGodsByNameKey();
        $gods = array_slice($gods, 1, 12, true);

        $duelRankedLeaderboard = $this->smite->getLeagueLeaderboard(440, 27, 6);
        $joustRankedLeaderboard = $this->smite->getLeagueLeaderboard(450, 27, 6);
        $conquestRankedLeaderboard = $this->smite->getLeagueLeaderboard(451, 27, 6);

        if (is_array($duelRankedLeaderboard)) {
            $duelRankedLeaderboard = array_slice($duelRankedLeaderboard, 0, 5, true);
        }
        if (is_array($joustRankedLeaderboard)) {
            $joustRankedLeaderboard = array_slice($joustRankedLeaderboard,  0, 5, true);
        }
        if (is_array($conquestRankedLeaderboard)) {
            $conquestRankedLeaderboard = array_slice($conquestRankedLeaderboard,  0, 5, true);
        }

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

    /**
     * @Route("/usage_stats", name="stats")
     * @return Response
     */
    public function stats(): Response
    {
        $apiCallRepo = $this->entityManager->getRepository(ApiCall::class);
        $usageStats = $apiCallRepo->getDailyApiUsageStats();

        return $this->render('index/stats.html.twig', [
            'usage_stats' => $usageStats
        ]);
    }
}
