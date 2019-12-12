<?php

namespace App\Controller;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;
use App\Entity\MatchPlayerBan;
use App\Entity\MatchPlayerItem;
use App\Entity\Player;
use App\Entity\PlayerSearch;
use App\Mapper\MatchPlayerMapper;
use App\Mapper\PlayerMapper;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class PlayerController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var MatchPlayerMapper */
    protected $matchPlayerMapper;

    /** @var PlayerMapper */
    protected $playerMapper;

    /** @var Smite */
    protected $smite;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MatchPlayerMapper $matchPlayerMapper,
        PlayerMapper $playerMapper,
        Smite $smite
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->matchPlayerMapper = $matchPlayerMapper;
        $this->playerMapper = $playerMapper;
        $this->smite = $smite;
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
        $newestPlayers = $playerRepo->findNewestPlayerNameNotNull(20);

        return $this->render('player/index.html.twig', [
            'newest_players' => $newestPlayers
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
        // Check to see if we have this player stored in the database
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        // See if we can find this player via the smite api
        if (is_null($player) || ($player->getCrawled() === 0)) {
            $playerDetails = $this->smite->getPlayerDetailsByPortalId($id);
            if (empty($playerDetails)) {
                throw new NotFoundHttpException();
            }

            $player = $this->playerMapper->from($playerDetails);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
        }

        $playerNameSlug = preg_replace('/\[.*?\]/is', '', $player->getName());
        $playerNameSlug = preg_replace('/([^a-z0-9-]+)/is', '', $playerNameSlug);
        if (empty($playerNameSlug)) {
            throw new NotFoundHttpException('An error occurred fetching a player name.');
        }

        $gods = $this->smite->getGodsByNameKey();

        $achievements = $this->smite->getPlayerAchievements($player->getSmitePlayerId()) ?? [];
        $matches = $this->smite->getPlayerMatches($player->getSmitePlayerId()) ?? [];

        $matchIds = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $matchIds[] = $match['Match'];
            }
        }

        // TODO Can check if we need to get match details of if they are already stored
        $matchIds = array_slice($matchIds, 0, 5, true);
        $matchDetails = $this->smite->getMatchDetailsBatch($matchIds);

        $formattedMatches = [];
        $teams = [];
        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {

                // TODO check if this allows multiple player_ids of 0 per match with unique god
                $existingMatch = $matchPlayerRepo->findOneBy([
                    'smiteMatchId' => $matchDetail['Match'],
                    'smitePlayerId' => $matchDetail['ActivePlayerId'],
                    'godId' => $matchDetail['GodId']
                ]);
                if (is_null($existingMatch)) {
                    $newMatchPlayer = $this->matchPlayerMapper->from($matchDetail);
                    $this->entityManager->persist($newMatchPlayer);

                    for ($i = 1; $i <= 6; $i++) {
                        $ability = new MatchPlayerAbility();
                        $ability->setAbilityId($matchDetail["ItemId{$i}"] ?: null);
                        $ability->setAbilityName($matchDetail["Item_Purch_{$i}"] ?: null);
                        $ability->setAbilityNumber($i);
                        $ability->setMatchPlayer($newMatchPlayer);
                        $this->entityManager->persist($ability);
                    }

                    for ($i = 1; $i <= 4; $i++) {
                        $item = new MatchPlayerItem();
                        $item->setItemId($matchDetail["ActiveId{$i}"] ?: null);
                        $item->setItemName($matchDetail["Item_Active_{$i}"] ?: null);
                        $item->setItemNumber($i);
                        $item->setMatchPlayer($newMatchPlayer);
                        $this->entityManager->persist($item);
                    }

                    for ($i = 1; $i <= 10; $i++) {
                        $ban = new MatchPlayerBan();
                        $ban->setBanId($matchDetail["Ban{$i}Id"] ?: null);
                        $ban->setBanName($matchDetail["Ban{$i}"] ?: null);
                        $ban->setBanNumber($i);
                        $ban->setMatchPlayer($newMatchPlayer);
                        $this->entityManager->persist($ban);
                    }
                }

                $teams[$matchDetail['Match']][$matchDetail['TaskForce']][] = $matchDetail;

                $formattedMatches[$matchDetail['Match']] = [
                    'Entry_Datetime' => $matchDetail['Entry_Datetime'],
                    'Map_Game' => $matchDetail['Map_Game'],
                    'Match' => $matchDetail['Match'],
                    'Minutes' => $matchDetail['Minutes'],
                    'Region' => $matchDetail['Region'],
                    'Winning_TaskForce' => $matchDetail['Winning_TaskForce'],
                    'Teams' => $teams[$matchDetail['Match']]
                ];
            }
            $this->entityManager->flush();
        }

        // FIXME seems to be a bug if team 1 wins but team 2 is ordered first
        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {
                $win = (array_key_exists($player->getSmitePlayerId(), $teams[$matchDetail['Match']][$matchDetail['Winning_TaskForce']]));
                $formattedMatches[$matchDetail['Match']]['Win'] = $win;
            }
        }

        $playerStats = [
            'Kills' => 0,
            'Assists' => 0,
            'Deaths' => 0,
        ];

        // TODO could store this with a PlayerGod entity
        $playerGods = $this->smite->getPlayerGodDetails($player->getSmitePlayerId()) ?? [];
        if (!empty($playerGods)) {
            foreach ($playerGods as $playerGod) {
                $playerStats['Kills'] += $playerGod['Kills'];
                $playerStats['Assists'] += $playerGod['Assists'];
                $playerStats['Deaths'] += $playerGod['Deaths'];
            }
        }

        $playerGodInfo = array_slice($playerGods, 0, 8, true);

        $playerUpdated = $player->getDateUpdated()->diff(new \DateTime());
        $playerUpdatedMins = $playerUpdated->days * 24 * 60;
        $playerUpdatedMins += $playerUpdated->h * 60;
        $playerUpdatedMins += $playerUpdated->i;

        return $this->render('player/player.html.twig', [
            'achievements' => $achievements,
            'last_updated' => $playerUpdatedMins,
            'player' => $player,
            'player_god_info' => $playerGodInfo,
            'player_stats' => $playerStats,
            'player_name_slug' => $playerNameSlug,
            'gods' => $gods,
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

            $players = $this->smite->searchPlayerByName($playerName);

            $playerRepo = $this->entityManager->getRepository(Player::class);
            /** @var Player $player */
            foreach ($players as $player) {
                $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $player['player_id']]);
                if (is_null($existingPlayer)) {
                    $newPlayer = new Player();
                    $newPlayer->setSmitePlayerId($player['player_id']);
                    $newPlayer->setDateCreated(new \DateTime());
                    $newPlayer->setDateUpdated(new \DateTime());
                    $this->entityManager->persist($newPlayer);
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
