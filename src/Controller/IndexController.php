<?php

namespace App\Controller;

use App\Entity\ApiCall;
use App\Service\GodService;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var GodService */
    protected $godService;

    /** @var SmiteService */
    protected $smiteService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GodService $godService,
        SmiteService $smiteService
    ) {
        $this->entityManager = $entityManager;
        $this->godService = $godService;
        $this->smiteService = $smiteService;
    }

    /**
     * @Route("/", name="homepage")
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(): Response
    {
        $gods = $this->godService->getFeaturedGods(12);

        $duelRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(440, 27, 7);
        $joustRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(450, 27, 7);
        $conquestRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(451, 27, 7);

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
