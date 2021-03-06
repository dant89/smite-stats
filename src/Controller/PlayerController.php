<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\PlayerAchievement;
use App\Entity\PlayerGod;
use App\Entity\PlayerSearch;
use App\Mapper\PlayerMapper;
use App\Service\GodService;
use App\Service\HelperService;
use App\Service\PlayerService;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class PlayerController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var PlayerMapper */
    protected $playerMapper;

    /** @var GodService */
    protected $godService;

    /** @var HelperService */
    protected $helperService;

    /** @var PlayerService */
    protected $playerService;

    /** @var SmiteService */
    protected $smiteService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        PlayerMapper $playerMapper,
        GodService $godService,
        HelperService $helperService,
        PlayerService $playerService,
        SmiteService $smiteService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->playerMapper = $playerMapper;
        $this->godService = $godService;
        $this->helperService = $helperService;
        $this->playerService = $playerService;
        $this->smiteService = $smiteService;
    }

    /**
     * @Route("/player/", name="players")
     * @param int id
     * @return Response
     */
    public function index(): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);

        /** @var Player $player */
        $newestPlayersQuery = $playerRepo->findNewestPlayerNameNotNullQuery(30);
        $newestPlayers = $newestPlayersQuery->execute();

        $playerLevelBands = $playerRepo->findCountOfPlayersByLevel();

        $bands = [];
        foreach ($playerLevelBands as $key => $playerLevelBand) {
            $bands[] = [
                'x' => (int) $playerLevelBand['count'],
                'y' => (int) $playerLevelBand['level']
            ];
        }

        # TODO move to somewhere better suited, itemService ?
        $tierMap = [
            1 => 'Bronze V',
            2 => 'Bronze IV',
            3 => 'Bronze III',
            4 => 'Bronze II',
            5 => 'Bronze I',
            6 => 'Silver V',
            7 => 'Silver IV',
            8 => 'Silver III',
            9 => 'Silver II',
            10 => 'Silver I',
            11 => 'Gold V',
            12 => 'Gold IV',
            13 => 'Gold III',
            14 => 'Gold II',
            15 => 'Gold I',
            16 => 'Platinum V',
            17 => 'Platinum IV',
            18 => 'Platinum III',
            19 => 'Platinum II',
            20 => 'Platinum I',
            21 => 'Diamond V',
            22 => 'Diamond IV',
            23 => 'Diamond III',
            24 => 'Diamond II',
            25 => 'Diamond I',
            26 => 'Masters I',
            27 => 'Grandmaster'
        ];

        $playersByTier = $playerRepo->findCountOfPlayersByTier('joust');
        $tiersData = [];
        foreach ($playersByTier as $key => $playerByTier) {
            if ($playerByTier['tier_joust'] !== "0" && $playerByTier['tier_sum'] !== "0") {
                $tiersData['labels'][] = $tierMap[$playerByTier['tier_joust']];
                $tiersData['data'][] = $playerByTier['tier_sum'];
            }
        }

        return $this->render('player/index.html.twig', [
            'newest_players' => $newestPlayers,
            'player_level_bands' => $bands,
            'player_tiers' => $tiersData
        ]);
    }

    /**
     * @Route("/player/{gamertag}-{id}", name="player_view", requirements={"gamertag": "[-\w]+"})
     * @param string $gamertag
     * @param int id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function player(string $gamertag, int $id): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $playerAchievementsRepo = $this->entityManager->getRepository(PlayerAchievement::class);
        $playerGodRepo = $this->entityManager->getRepository(PlayerGod::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        $playerUpdatedMins = 1;
        if (!is_null($player)) {
            $playerUpdatedMins = $this->helperService->getMinutesLastUpdated($player->getDateUpdated());
        }

        // Check to see if we have this player stored in the database
        if (is_null($player)) {
            $playerDetails = $this->smiteService->getPlayerDetailsByPortalId($id);
            if (empty($playerDetails) || ((int) $playerDetails['ActivePlayerId'] === 0)) {
                // Player not in database an no details returned
                throw new NotFoundHttpException();
            } else {
                // Create and store player
                $player = $this->playerMapper->from($playerDetails);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
        } elseif ($player->getCrawled() === 0 || $playerUpdatedMins > (60 * 24)) {
            $playerDetails = $this->smiteService->getPlayerDetailsByPortalId($id);
            if (!empty($playerDetails) && ((int) $playerDetails['ActivePlayerId'] !== 0)) {
                // Update player
                $player = $this->playerMapper->fromExisting($player, $playerDetails);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
                $playerUpdatedMins = $this->helperService->getMinutesLastUpdated($player->getDateUpdated());
            }
        }

        $playerNameSlug = preg_replace('/\[.*?\]/is', '', $player->getName());
        $playerNameSlug = preg_replace('/([^a-z0-9-]+)/is', '', $playerNameSlug);
        if (empty($playerNameSlug)) {
            throw new NotFoundHttpException('An error occurred fetching a player name.');
        }

        /** @var PlayerAchievement $playerAchievements */
        $playerAchievements = $playerAchievementsRepo->findOneBy(['smitePlayer' => $player]);
        if (is_null($playerAchievements) ||
            $this->helperService->getMinutesLastUpdated($playerAchievements->getDateUpdated()) > 30
        ) {

            $this->playerService->updatePlayerAchievements($player);
            $playerAchievements = $playerAchievementsRepo->findOneBy(['smitePlayer' => $player]);
        }

        // Get Player Matches
        $playerMatches = $this->playerService->getPlayerMatches($player, 10);

        // If the Players God details were updated in the last 24 hours, use database info
        if (is_null($player->getGodsDateUpdated()) ||
            $this->helperService->getMinutesLastUpdated($player->getGodsDateUpdated()) > (60 * 24)
        ) {
            $this->playerService->updatePlayerGods($player);
        }
        $playerGods = $playerGodRepo->findBy(['smitePlayer' => $player], ['rank' => 'DESC']);

        $gods = $this->godService->getGodsByNameKey();

        return $this->render('player/player.html.twig', [
            'achievements' => $playerAchievements,
            'last_updated' => $playerUpdatedMins,
            'player' => $player,
            'player_god_info' => $playerGods,
            'gods' => $gods,
            'matches' => $playerMatches
        ]);
    }

    /**
     * @Route("/player/{gamertag}-{id}/gods", name="player_gods_view", requirements={"gamertag": "[-\w]+"})
     * @param string $gamertag
     * @param int id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function playerGods(string $gamertag, int $id): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $playerGodRepo = $this->entityManager->getRepository(PlayerGod::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);
        if (is_null($player)) {
            throw new NotFoundHttpException();
        }

        // If the Players God details were updated in the last 24 hours, use database info
        if (is_null($player->getGodsDateUpdated()) ||
            $this->helperService->getMinutesLastUpdated($player->getGodsDateUpdated()) > (60 * 24)
        ) {
            $this->playerService->updatePlayerGods($player);
        }

        $gods = $this->godService->getGodsByNameKey();
        $playerGods = $playerGodRepo->findBy(['smitePlayer' => $player], ['rank' => 'DESC']);

        return $this->render('player/gods.html.twig', [
            'player' => $player,
            'player_god_info' => $playerGods,
            'gods' => $gods
        ]);
    }

    /**
     * @Route("/player/{gamertag}-{id}/matches", name="player_matches_view", requirements={"gamertag": "[-\w]+"})
     * @param string $gamertag
     * @param int id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function playerMatches(string $gamertag, int $id): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);
        if (is_null($player)) {
            throw new NotFoundHttpException();
        }

        $formattedMatches = $this->playerService->getPlayerMatches($player, 10);

        $playerUpdatedMins = $this->helperService->getMinutesLastUpdated($player->getDateUpdated());

        return $this->render('player/matches.html.twig', [
            'player' => $player,
            'last_updated' => $playerUpdatedMins,
            'matches' => $formattedMatches
        ]);
    }

    /**
     * @Route("/player_search", name="player_search")
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function playerSearch(Request $request): Response
    {
        $playerName = $request->get('player_name');

        $players = [];
        if (!is_null($playerName) && !empty($playerName)) {

            $playerSearch = new PlayerSearch();
            $playerSearch->setTerm($playerName);
            $playerSearch->setDateSearched(new \DateTime());
            $this->entityManager->persist($playerSearch);
            $this->entityManager->flush();

            $players = $this->smiteService->searchPlayerByName($playerName);
            $playerRepo = $this->entityManager->getRepository(Player::class);

            /** @var Player $player */
            if ($players) {
                foreach ($players as $player) {
                    $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $player['player_id']]);
                    if (is_null($existingPlayer)) {
                        $playerId = (int) $player['player_id'];
                        if ($playerId !== 0) {
                            $newPlayer = new Player();
                            $newPlayer->setSmitePlayerId($playerId);
                            $newPlayer->setDateCreated(new \DateTime());
                            $newPlayer->setDateUpdated(new \DateTime());
                            $this->entityManager->persist($newPlayer);
                        }
                    }
                }
                $this->entityManager->flush();
            }
        }

        return $this->render('player/search.html.twig', [
            'players' => $players,
            'search_term' => $playerName
        ]);
    }
}