<?php

namespace App\Controller;

use App\Entity\ApiCall;
use App\Service\GodService;
use App\Service\PlayerService;
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

    /** @var PlayerService */
    protected $playerService;

    /** @var SmiteService */
    protected $smiteService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GodService $godService,
        PlayerService $playerService,
        SmiteService $smiteService
    ) {
        $this->entityManager = $entityManager;
        $this->godService = $godService;
        $this->playerService = $playerService;
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

        $topKillGods = $this->godService->getTopKillGods();
        $topKdGods = $this->godService->getTopKdGods();
        $topKdaGods = $this->godService->getTopKdaGods();

        $playerLevels = $this->playerService->getPlayerLevels();
        $playerWorshippers = $this->playerService->getPlayerWorshippers();

        # TODO replace with database store of top players
        $duelRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(440, 27, 7);
        $joustRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(450, 27, 7);
        $conquestRankedLeaderboard = $this->smiteService->getLeagueLeaderboard(451, 27, 7);

        $leaderboardTypes = [];

        if (is_array($duelRankedLeaderboard)) {
            $leaderboardTypes['Duel'] = array_slice($duelRankedLeaderboard, 0, 5, true);
        }
        if (is_array($joustRankedLeaderboard)) {
            $leaderboardTypes['Joust'] = array_slice($joustRankedLeaderboard,  0, 5, true);
        }
        if (is_array($conquestRankedLeaderboard)) {
            $leaderboardTypes['Conquest'] = array_slice($conquestRankedLeaderboard,  0, 5, true);
        }

        return $this->render('index/index.html.twig', [
            'gods' => $gods,
            'player_levels' => $playerLevels,
            'player_worshippers' => $playerWorshippers,
            'top_kills_gods' => $topKillGods,
            'top_kd_gods' => $topKdGods,
            'top_kda_gods' => $topKdaGods,
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
