<?php

namespace App\Controller;

use App\Entity\God;
use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;
use App\Entity\MatchPlayerBan;
use App\Entity\MatchPlayerItem;
use App\Entity\Player;
use App\Entity\PlayerGod;
use App\Entity\PlayerSearch;
use App\Mapper\MatchPlayerMapper;
use App\Mapper\PlayerMapper;
use App\Service\Smite;
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
        $newestPlayersQuery = $playerRepo->findNewestPlayerNameNotNullQuery(30);
        $newestPlayers = $newestPlayersQuery->execute();

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
        // TODO controller contains too much logic - tidy

        // Check to see if we have this player stored in the database
        $godRepo = $this->entityManager->getRepository(God::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $playerGodRepo = $this->entityManager->getRepository(PlayerGod::class);
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        // See if we can find this player via the smite api
        if (is_null($player) || ($player->getCrawled() === 0)) {
            $playerDetails = $this->smite->getPlayerDetailsByPortalId($id);
            if (empty($playerDetails)) {
                throw new NotFoundHttpException();
            }
            // Create player
            if (is_null($player) && $playerDetails['ActivePlayerId'] !== "0") {
                $player = $this->playerMapper->from($playerDetails);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            } else {
                $player = $this->playerMapper->fromExisting($player, $playerDetails);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
        }

        $playerNameSlug = preg_replace('/\[.*?\]/is', '', $player->getName());
        $playerNameSlug = preg_replace('/([^a-z0-9-]+)/is', '', $playerNameSlug);
        if (empty($playerNameSlug)) {
            throw new NotFoundHttpException('An error occurred fetching a player name.');
        }

        $gods = $this->smite->getGodsByNameKey();

        # TODO uses an api request, store or cache?
        $achievements = $this->smite->getPlayerAchievements($player->getSmitePlayerId()) ?? [];

        // Handle player matches
        $existingMatchIds = [];
        $matchPlayerIds = $matchPlayerRepo->getUniqueMatchIds($player);
        foreach ($matchPlayerIds as $matchPlayerId) {
            if (!in_array($matchPlayerId['smiteMatchId'], $existingMatchIds)) {
                $existingMatchIds[] = $matchPlayerId['smiteMatchId'];
            }
        }

        # TODO uses an api request, store or cache?
        $matches = $this->smite->getPlayerMatches($player->getSmitePlayerId()) ?? [];
        $recentMatchIds = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $recentMatchIds[] = $match['Match'];
            }
        }

        // TODO get the latest stored matches for a player
        // Need a function here to check which of the latest 50 match IDs are not stored in the database
        // We can then just batch those IDs and return the data for a smaller subset rather than
        // 5x 10 match queries which is very slow!!

        $formattedMatches = [];

        // TODO Can check if we need to get match details of if they are already stored
        // TODO Check if in array of returned matches, if not crawl then reorder on match ID desc
        $recentMatchIds = array_slice($recentMatchIds, 0, 10, true);
        $matchDetails = $this->smite->getMatchDetailsBatch($recentMatchIds);

        // TODO store the latest matches, then query the database for matches by id for user - simple solution!

        $teams = [];

        // TODO this is far too slow!!! if can't speed up, use a bus and crawled 0 by default and show a
        // TODO 'getting match details' periodically until crawl has completed

        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {

                // Search by the unique index constraint on PlayerMatch
                $storedMatch = $matchPlayerRepo->findOneBy([
                    'smiteMatchId' => $matchDetail['Match'],
                    'godId' => $matchDetail['GodId'],
                    'taskForce' => $matchDetail['TaskForce']
                ]);

                // TODO send to message bus
                if (is_null($storedMatch)) {

                    /** @var Player $existingPlayer */
                    $existingPlayer = $playerRepo->findOneBy([
                        'smitePlayerId' => (int)$matchDetail['ActivePlayerId']
                    ]);
                    if (is_null($existingPlayer) && $matchDetail['ActivePlayerId'] !== "0") {
                        $playerDetails = $this->smite->getPlayerDetailsByPortalId($matchDetail['ActivePlayerId']);
                        if (!empty($playerDetails)) {
                            $existingPlayerMapped = $this->playerMapper->from($playerDetails);
                            if ($existingPlayerMapped->getSmitePlayerId() !== 0) {
                                $this->entityManager->persist($existingPlayerMapped);
                                $this->entityManager->flush();
                            } else {
                                // Exception - Timestamp
                                $existingPlayerMapped = null;
                                throw new \Exception('this is broken');
                            }
                        }
                        $existingPlayer = $existingPlayerMapped;
                    }

                    if (!is_null($existingPlayer)) {
                        $storedMatch = $this->matchPlayerMapper->from($matchDetail, $existingPlayer);
                        $this->entityManager->persist($storedMatch);
                        // Match has to be created before it can be used for sub entities
                        $this->entityManager->flush();

                        for ($i = 1; $i <= 6; $i++) {
                            $ability = new MatchPlayerAbility();
                            $ability->setAbilityId($matchDetail["ItemId{$i}"] ?: null);
                            $ability->setAbilityName($matchDetail["Item_Purch_{$i}"] ?: null);
                            $ability->setAbilityNumber($i);
                            $ability->setMatchPlayer($storedMatch);
                            $this->entityManager->persist($ability);
                        }

                        for ($i = 1; $i <= 4; $i++) {
                            $item = new MatchPlayerItem();
                            $item->setItemId($matchDetail["ActiveId{$i}"] ?: null);
                            $item->setItemName($matchDetail["Item_Active_{$i}"] ?: null);
                            $item->setItemNumber($i);
                            $item->setMatchPlayer($storedMatch);
                            $this->entityManager->persist($item);
                        }

                        for ($i = 1; $i <= 10; $i++) {
                            $ban = new MatchPlayerBan();
                            $ban->setBanId($matchDetail["Ban{$i}Id"] ?: null);
                            $ban->setBanName($matchDetail["Ban{$i}"] ?: null);
                            $ban->setBanNumber($i);
                            $ban->setMatchPlayer($storedMatch);
                            $this->entityManager->persist($ban);
                        }

                        $this->entityManager->flush();
                    }
                }

                if ($storedMatch instanceof MatchPlayer) {
                    $teams[$storedMatch->getSmiteMatchId()][$storedMatch->getTaskForce()][] = $storedMatch;
                    $formattedMatches[$storedMatch->getSmiteMatchId()] = [
                        'Entry_Datetime' => $storedMatch->getEntryDatetime(),
                        'Map_Game' => $storedMatch->getMapGame(),
                        'Match' => $storedMatch->getSmiteMatchId(),
                        'Minutes' => $storedMatch->getMinutes(),
                        'Region' => $storedMatch->getRegion(),
                        'Teams' => $teams[$storedMatch->getSmiteMatchId()]
                    ];
                }
            }
        }

        if (!empty($formattedMatches)) {
            foreach ($formattedMatches as &$formattedMatch) {
                $formattedMatch['Winning_TaskForce'] = $formattedMatch['Teams'][1][0]->getWinningTaskForce();

                $matchPlayers = $formattedMatch['Teams'][$formattedMatch['Winning_TaskForce']];
                $winner = false;
                /** @var MatchPlayer $matchPlayer */
                foreach ($matchPlayers as $matchPlayer) {
                    /** @var Player $smitePlayer */
                    $smitePlayer = $matchPlayer->getSmitePlayer();
                    if (!is_null($smitePlayer)) {
                        $smitePlayerId = (int) $smitePlayer->getSmitePlayerId();
                        if ($smitePlayerId === $id) {
                            $winner = true;
                        }
                    }
                }

                $formattedMatch['Player_Won'] = $winner;
            }
        }

        if (!is_null($player->getGodsDateUpdated())) {
            $playerGodsUpdated = $player->getGodsDateUpdated()->diff(new \DateTime());
            $playerGodsUpdatedMins = $playerGodsUpdated->days * 24 * 60;
            $playerGodsUpdatedMins += $playerGodsUpdated->h * 60;
            $playerGodsUpdatedMins += $playerGodsUpdated->i;
        } else {
            $playerGodsUpdatedMins = (61 * 24);
        }

        // If the God details were updated in the last 24 hours, use database info
        if ($playerGodsUpdatedMins > (60 * 24)) {
            $playerGods = $this->smite->getPlayerGodDetails($player->getSmitePlayerId()) ?? [];
            if (!empty($playerGods)) {
                foreach ($playerGods as $playerGod) {

                    $god = $godRepo->findOneBy(['smiteId' => $playerGod['god_id']]);
                    if (!is_null($god)) {
                        /** @var PlayerGod $existingPlayerGod */
                        $existingPlayerGod = $playerGodRepo->findOneBy([
                            'god' => $god,
                            'smitePlayer' => $player
                        ]);
                        if (is_null($existingPlayerGod)) {
                            $storedPlayerGod = new PlayerGod();
                            $storedPlayerGod->setGod($god);
                            $storedPlayerGod->setSmitePlayer($player);
                            $storedPlayerGod->setAssists($playerGod['Assists']);
                            $storedPlayerGod->setDeaths($playerGod['Deaths']);
                            $storedPlayerGod->setKills($playerGod['Kills']);
                            $storedPlayerGod->setLosses($playerGod['Losses']);
                            $storedPlayerGod->setMinionKills($playerGod['MinionKills']);
                            $storedPlayerGod->setRank($playerGod['Rank']);
                            $storedPlayerGod->setWins($playerGod['Wins']);
                            $storedPlayerGod->setWorshippers($playerGod['Worshippers']);
                            $storedPlayerGod->setDateCreated(new \DateTime());
                            $storedPlayerGod->setDateUpdated(new \DateTime());
                            $player->setGodsDateUpdated(new \DateTime());
                            $this->entityManager->persist($storedPlayerGod);
                            $this->entityManager->persist($player);
                        } else {
                            $existingPlayerGod->setAssists($playerGod['Assists']);
                            $existingPlayerGod->setDeaths($playerGod['Deaths']);
                            $existingPlayerGod->setKills($playerGod['Kills']);
                            $existingPlayerGod->setLosses($playerGod['Losses']);
                            $existingPlayerGod->setMinionKills($playerGod['MinionKills']);
                            $existingPlayerGod->setRank($playerGod['Rank']);
                            $existingPlayerGod->setWins($playerGod['Wins']);
                            $existingPlayerGod->setWorshippers($playerGod['Worshippers']);
                            $existingPlayerGod->setDateUpdated(new \DateTime());
                            $player->setGodsDateUpdated(new \DateTime());
                            $this->entityManager->persist($existingPlayerGod);
                            $this->entityManager->persist($player);
                        }
                    }
                }
                $this->entityManager->flush();
            }
        }

        $playerGods = $playerGodRepo->findBy(['smitePlayer' => $player], ['rank' => 'DESC']);

        $playerUpdated = $player->getDateUpdated()->diff(new \DateTime());
        $playerUpdatedMins = $playerUpdated->days * 24 * 60;
        $playerUpdatedMins += $playerUpdated->h * 60;
        $playerUpdatedMins += $playerUpdated->i;

        return $this->render('player/player.html.twig', [
            'achievements' => $achievements,
            'last_updated' => $playerUpdatedMins,
            'player' => $player,
            'player_god_info' => $playerGods,
            'player_name_slug' => $playerNameSlug,
            'gods' => $gods,
            'matches' => $formattedMatches
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
        // Check to see if we have this player stored in the database
        $godRepo = $this->entityManager->getRepository(God::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $playerGodRepo = $this->entityManager->getRepository(PlayerGod::class);

        $gods = $this->smite->getGodsByNameKey();

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        // See if we can find this player via the smite api
        if (is_null($player)) {
            throw new NotFoundHttpException();
        }

        if (!is_null($player->getGodsDateUpdated())) {
            $playerGodsUpdated = $player->getGodsDateUpdated()->diff(new \DateTime());
            $playerGodsUpdatedMins = $playerGodsUpdated->days * 24 * 60;
            $playerGodsUpdatedMins += $playerGodsUpdated->h * 60;
            $playerGodsUpdatedMins += $playerGodsUpdated->i;
        } else {
            $playerGodsUpdatedMins = (61 * 24);
        }

        // If the God details were updated in the last 24 hours, use database info
        if ($playerGodsUpdatedMins > (60 * 24)) {
            $playerGods = $this->smite->getPlayerGodDetails($player->getSmitePlayerId()) ?? [];
            if (!empty($playerGods)) {
                foreach ($playerGods as $playerGod) {

                    $god = $godRepo->findOneBy(['smiteId' => $playerGod['god_id']]);
                    if (!is_null($god)) {
                        /** @var PlayerGod $existingPlayerGod */
                        $existingPlayerGod = $playerGodRepo->findOneBy([
                            'god' => $god,
                            'smitePlayer' => $player
                        ]);
                        if (is_null($existingPlayerGod)) {
                            $storedPlayerGod = new PlayerGod();
                            $storedPlayerGod->setGod($god);
                            $storedPlayerGod->setSmitePlayer($player);
                            $storedPlayerGod->setAssists($playerGod['Assists']);
                            $storedPlayerGod->setDeaths($playerGod['Deaths']);
                            $storedPlayerGod->setKills($playerGod['Kills']);
                            $storedPlayerGod->setLosses($playerGod['Losses']);
                            $storedPlayerGod->setMinionKills($playerGod['MinionKills']);
                            $storedPlayerGod->setRank($playerGod['Rank']);
                            $storedPlayerGod->setWins($playerGod['Wins']);
                            $storedPlayerGod->setWorshippers($playerGod['Worshippers']);
                            $storedPlayerGod->setDateCreated(new \DateTime());
                            $storedPlayerGod->setDateUpdated(new \DateTime());
                            $player->setGodsDateUpdated(new \DateTime());
                            $this->entityManager->persist($storedPlayerGod);
                            $this->entityManager->persist($player);
                        } else {
                            $existingPlayerGod->setAssists($playerGod['Assists']);
                            $existingPlayerGod->setDeaths($playerGod['Deaths']);
                            $existingPlayerGod->setKills($playerGod['Kills']);
                            $existingPlayerGod->setLosses($playerGod['Losses']);
                            $existingPlayerGod->setMinionKills($playerGod['MinionKills']);
                            $existingPlayerGod->setRank($playerGod['Rank']);
                            $existingPlayerGod->setWins($playerGod['Wins']);
                            $existingPlayerGod->setWorshippers($playerGod['Worshippers']);
                            $existingPlayerGod->setDateUpdated(new \DateTime());
                            $player->setGodsDateUpdated(new \DateTime());
                            $this->entityManager->persist($existingPlayerGod);
                            $this->entityManager->persist($player);
                        }
                    }
                }
                $this->entityManager->flush();
            }
        }

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
        // Check to see if we have this player stored in the database
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        // See if we can find this player via the smite api
        if (is_null($player)) {
            throw new NotFoundHttpException();
        }

        // Handle player matches
        $existingMatchIds = [];
        $matchPlayerIds = $matchPlayerRepo->getUniqueMatchIds($player);
        foreach ($matchPlayerIds as $matchPlayerId) {
            if (!in_array($matchPlayerId['smiteMatchId'], $existingMatchIds)) {
                $existingMatchIds[] = $matchPlayerId['smiteMatchId'];
            }
        }

        # TODO uses an api request, store or cache?
        $matches = $this->smite->getPlayerMatches($player->getSmitePlayerId()) ?? [];
        $recentMatchIds = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $recentMatchIds[] = $match['Match'];
            }
        }

        // TODO get the latest stored matches for a player
        // Need a function here to check which of the latest 50 match IDs are not stored in the database
        // We can then just batch those IDs and return the data for a smaller subset rather than
        // 5x 10 match queries which is very slow!!

        $formattedMatches = [];

        // we get 50 match IDs, lets split them into chunks of 5
        $matchIdsChunks = array_chunk($recentMatchIds, 5);
        if (!empty($matchIdsChunks)) {
            foreach ($matchIdsChunks as $matchIdsChunk) {

                // TODO Can check if we need to get match details of if they are already stored
                // TODO Check if in array of returned matches, if not crawl then reorder on match ID desc
                $recentMatchIds = array_slice($matchIdsChunk, 0, 10, true);
                $matchDetails = $this->smite->getMatchDetailsBatch($recentMatchIds);

                // TODO store the latest matches, then query the database for matches by id for user - simple solution!

                $teams = [];

                // TODO this is far too slow!!! if can't speed up, use a bus and crawled 0 by default and show a
                // TODO 'getting match details' periodically until crawl has completed

                if (!empty($matchDetails)) {
                    foreach ($matchDetails as $matchDetail) {

                        // Search by the unique index constraint on PlayerMatch
                        $storedMatch = $matchPlayerRepo->findOneBy([
                            'smiteMatchId' => $matchDetail['Match'],
                            'godId' => $matchDetail['GodId'],
                            'taskForce' => $matchDetail['TaskForce']
                        ]);

                        // TODO send to message bus
                        if (is_null($storedMatch)) {

                            /** @var Player $existingPlayer */
                            $existingPlayer = $playerRepo->findOneBy([
                                'smitePlayerId' => (int) $matchDetail['ActivePlayerId']
                            ]);
                            if (is_null($existingPlayer) && $matchDetail['ActivePlayerId'] !== "0") {
                                $playerDetails = $this->smite->getPlayerDetailsByPortalId($matchDetail['ActivePlayerId']);
                                if (!empty($playerDetails)) {
                                    $existingPlayerMapped = $this->playerMapper->from($playerDetails);
                                    if ($existingPlayerMapped->getSmitePlayerId() !== 0) {
                                        $this->entityManager->persist($existingPlayerMapped);
                                        $this->entityManager->flush();
                                    } else {
                                        // Exception - Timestamp
                                        $existingPlayerMapped = null;
                                        throw new \Exception('this is broken');
                                    }
                                }
                                $existingPlayer = $existingPlayerMapped;
                            }

                            if (!is_null($existingPlayer)) {
                                $storedMatch = $this->matchPlayerMapper->from($matchDetail, $existingPlayer);
                                $this->entityManager->persist($storedMatch);
                                // Match has to be created before it can be used for sub entities
                                $this->entityManager->flush();

                                for ($i = 1; $i <= 6; $i++) {
                                    $ability = new MatchPlayerAbility();
                                    $ability->setAbilityId($matchDetail["ItemId{$i}"] ?: null);
                                    $ability->setAbilityName($matchDetail["Item_Purch_{$i}"] ?: null);
                                    $ability->setAbilityNumber($i);
                                    $ability->setMatchPlayer($storedMatch);
                                    $this->entityManager->persist($ability);
                                }

                                for ($i = 1; $i <= 4; $i++) {
                                    $item = new MatchPlayerItem();
                                    $item->setItemId($matchDetail["ActiveId{$i}"] ?: null);
                                    $item->setItemName($matchDetail["Item_Active_{$i}"] ?: null);
                                    $item->setItemNumber($i);
                                    $item->setMatchPlayer($storedMatch);
                                    $this->entityManager->persist($item);
                                }

                                for ($i = 1; $i <= 10; $i++) {
                                    $ban = new MatchPlayerBan();
                                    $ban->setBanId($matchDetail["Ban{$i}Id"] ?: null);
                                    $ban->setBanName($matchDetail["Ban{$i}"] ?: null);
                                    $ban->setBanNumber($i);
                                    $ban->setMatchPlayer($storedMatch);
                                    $this->entityManager->persist($ban);
                                }

                                $this->entityManager->flush();
                            }
                        }

                        if ($storedMatch instanceof MatchPlayer) {
                            $teams[$storedMatch->getSmiteMatchId()][$storedMatch->getTaskForce()][] = $storedMatch;
                            $formattedMatches[$storedMatch->getSmiteMatchId()] = [
                                'Entry_Datetime' => $storedMatch->getEntryDatetime(),
                                'Map_Game' => $storedMatch->getMapGame(),
                                'Match' => $storedMatch->getSmiteMatchId(),
                                'Minutes' => $storedMatch->getMinutes(),
                                'Region' => $storedMatch->getRegion(),
                                'Teams' => $teams[$storedMatch->getSmiteMatchId()]
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($formattedMatches)) {
            foreach ($formattedMatches as &$formattedMatch) {
                $formattedMatch['Winning_TaskForce'] = $formattedMatch['Teams'][1][0]->getWinningTaskForce();

                $matchPlayers = $formattedMatch['Teams'][$formattedMatch['Winning_TaskForce']];
                $winner = false;
                /** @var MatchPlayer $matchPlayer */
                foreach ($matchPlayers as $matchPlayer) {
                    /** @var Player $smitePlayer */
                    $smitePlayer = $matchPlayer->getSmitePlayer();
                    if (!is_null($smitePlayer)) {
                        $smitePlayerId = (int) $smitePlayer->getSmitePlayerId();
                        if ($smitePlayerId === $id) {
                            $winner = true;
                        }
                    }
                }

                $formattedMatch['Player_Won'] = $winner;
            }
        }

        $playerUpdated = $player->getDateUpdated()->diff(new \DateTime());
        $playerUpdatedMins = $playerUpdated->days * 24 * 60;
        $playerUpdatedMins += $playerUpdated->h * 60;
        $playerUpdatedMins += $playerUpdated->i;

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

            $players = $this->smite->searchPlayerByName($playerName);
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