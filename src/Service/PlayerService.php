<?php

namespace App\Service;

use App\Entity\God;
use App\Entity\MatchItem;
use App\Entity\MatchPlayer;
use App\Entity\Player;
use App\Entity\PlayerGod;
use App\Mapper\MatchPlayerAbilityMapper;
use App\Mapper\MatchPlayerBanMapper;
use App\Mapper\MatchPlayerItemMapper;
use App\Mapper\MatchPlayerMapper;
use App\Mapper\PlayerMapper;
use App\Repository\MatchPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use \Psr\Cache\InvalidArgumentException;

class PlayerService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var MatchPlayerMapper */
    protected $matchPlayerMapper;

    /** @var MatchPlayerAbilityMapper */
    protected $matchPlayerAbilityMapper;

    /** @var MatchPlayerBanMapper */
    protected $matchPlayerBanMapper;

    /** @var MatchPlayerItemMapper */
    protected $matchPlayerItemMapper;

    /** @var PlayerMapper */
    protected $playerMapper;

    /** @var SmiteService */
    protected $smite;

    public function __construct(
        EntityManagerInterface $entityManager,
        MatchPlayerMapper $matchPlayerMapper,
        MatchPlayerAbilityMapper $matchPlayerAbilityMapper,
        MatchPlayerBanMapper $matchPlayerBanMapper,
        MatchPlayerItemMapper $matchPlayerItemMapper,
        PlayerMapper $playerMapper,
        SmiteService $smite
    ) {
        $this->entityManager = $entityManager;
        $this->matchPlayerMapper = $matchPlayerMapper;
        $this->matchPlayerAbilityMapper = $matchPlayerAbilityMapper;
        $this->matchPlayerBanMapper = $matchPlayerBanMapper;
        $this->matchPlayerItemMapper = $matchPlayerItemMapper;
        $this->playerMapper = $playerMapper;
        $this->smite = $smite;
    }

    /**
     * @param Player $player
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPlayerMatches(Player $player, $limit = 5): array
    {
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);

        // Handle player matches
        $existingMatchIds = [];
        /** @var MatchPlayerRepository $matchPlayerRepo */
        $matchPlayerIds = $matchPlayerRepo->getUniqueMatchIds($player);
        foreach ($matchPlayerIds as $matchPlayerId) {
            if (!in_array($matchPlayerId['smiteMatchId'], $existingMatchIds)) {
                $existingMatchIds[] = $matchPlayerId['smiteMatchId'];
            }
        }

        // TODO can we check when the players matches were updated to avoid having to query every single time?

        // TODO uses an api request, store or cache?
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

        // TODO store the latest matches, then query the database for matches by id for user - simple solution!

        $teams = [];
        $recentMatchIdsLimited = array_slice($recentMatchIds, 0, $limit, true);

        $matchIdsChunks = array_chunk($recentMatchIdsLimited, 5);
        if (!empty($matchIdsChunks)) {
            foreach ($matchIdsChunks as $matchIdsChunk) {

                // TODO is this the slow bit?!
                $matchesData = $this->smite->getMatchDetailsBatch($matchIdsChunk);

                if (!empty($matchesData)) {
                    foreach ($matchesData as $matchData) {

                        // FIXME somehow a duplicate entry error occurs here
                        // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1001133775-2-1924'
                        // for key 'unique_match_player_id'

                        // Search by the unique index constraint on PlayerMatch
                        $storedMatchPlayer = $matchPlayerRepo->findOneBy([
                            'smiteMatchId' => $matchData['Match'],
                            'godId' => $matchData['GodId'],
                            'taskForce' => $matchData['TaskForce']
                        ]);
                        if (is_null($storedMatchPlayer)) {
                            $storedMatchPlayer = $this->storeNewMatchPlayer($matchData);
                        }

                        if ($storedMatchPlayer instanceof MatchPlayer) {
                            $teams[$storedMatchPlayer->getSmiteMatchId()][$storedMatchPlayer->getTaskForce()][] = $storedMatchPlayer;
                            $formattedMatches[$storedMatchPlayer->getSmiteMatchId()] = [
                                'Entry_Datetime' => $storedMatchPlayer->getEntryDatetime(),
                                'Map_Game' => $storedMatchPlayer->getMapGame(),
                                'Minutes' => $storedMatchPlayer->getMinutes(),
                                'Teams' => $teams[$storedMatchPlayer->getSmiteMatchId()]
                            ];
                        }
                    }
                }
            }
            unset($recentMatchIdsLimited);
            unset($matchIdsChunks);
            unset($matchesData);
            unset($storedMatchPlayer);
            unset($teams);
        }

        return $this->formatMatches($player->getSmitePlayerId(), $formattedMatches);
    }

    protected function formatMatches(int $playerId, array $formattedMatches): array
    {
        if (!empty($formattedMatches)) {
            foreach ($formattedMatches as &$formattedMatch) {

                $formattedMatch['Winning_TaskForce'] = $formattedMatch['Teams'][1][0]->getWinningTaskForce();
                $formattedMatch['Player_Won'] = false;

                foreach ($formattedMatch['Teams'] as $teamPlayers) {
                    /** @var MatchPlayer $matchPlayer */
                    foreach ($teamPlayers as $matchPlayer) {
                        /** @var Player $smitePlayer */
                        $smitePlayer = $matchPlayer->getSmitePlayer();
                        if (!is_null($smitePlayer)) {
                            $smitePlayerId = (int) $smitePlayer->getSmitePlayerId();

                            if ($smitePlayerId === $playerId) {
                                $formattedMatch['Player_Stats'] = [
                                    'kills' => $matchPlayer->getKillsPlayer(),
                                    'deaths' => $matchPlayer->getDeaths(),
                                    'assists' => $matchPlayer->getAssists(),
                                ];
                                if ($formattedMatch['Winning_TaskForce'] === $matchPlayer->getTaskForce()) {
                                    $formattedMatch['Player_Won'] = true;
                                }
                            }
                        }
                    }
                }
            }
            unset($formattedMatch);
        }

        return $formattedMatches;
    }

    /**
     * @param array $matchPlayerData
     * @return MatchPlayer
     * @throws InvalidArgumentException
     */
    private function storeNewMatchPlayer(array $matchPlayerData): MatchPlayer
    {
        $matchItemRepo = $this->entityManager->getRepository(MatchItem::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);

        /** @var Player $existingPlayer */
        $existingPlayer = $playerRepo->findOneBy([
            'smitePlayerId' => (int)$matchPlayerData['ActivePlayerId']
        ]);
        if (is_null($existingPlayer) && $matchPlayerData['ActivePlayerId'] !== "0") {
            $playerDetails = $this->smite->getPlayerDetailsByPortalId($matchPlayerData['ActivePlayerId']);
            if (!empty($playerDetails)) {
                $existingPlayerMapped = $this->playerMapper->from($playerDetails);
                if ($existingPlayerMapped->getSmitePlayerId() !== 0) {
                    $this->entityManager->persist($existingPlayerMapped);
                    $this->entityManager->flush();
                    $existingPlayer = $existingPlayerMapped;
                }
            }
        }

        $storedMatch = $this->matchPlayerMapper->from($matchPlayerData, $existingPlayer);
        $this->entityManager->persist($storedMatch);

        for ($i = 1; $i <= 4; $i++) {
            if (isset($matchPlayerData["ActiveId{$i}"])) {
                /** @var MatchItem $storedMatchItem */
                $storedMatchItem = $matchItemRepo->findOneBy(
                    ['itemId' => $matchPlayerData["ActiveId{$i}"]]
                );
                if (!is_null($storedMatchItem)) {
                    $matchPlayerAbility = $this->matchPlayerAbilityMapper->from(
                        $i,
                        $storedMatch,
                        $storedMatchItem
                    );
                    $this->entityManager->persist($matchPlayerAbility);
                }
            }
        }

        for ($i = 1; $i <= 6; $i++) {
            if (isset($matchDetail["ItemId{$i}"])) {
                /** @var MatchItem $storedMatchItem */
                $storedMatchItem = $matchItemRepo->findOneBy([
                    'itemId' => $matchPlayerData["ItemId{$i}"]
                ]);
                if (!is_null($storedMatchItem)) {
                    $matchPlayerItem = $this->matchPlayerItemMapper->from(
                        $i,
                        $storedMatch,
                        $storedMatchItem
                    );
                    $this->entityManager->persist($matchPlayerItem);
                }
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            $matchPlayerBan = $this->matchPlayerBanMapper->from($matchPlayerData, $i, $storedMatch);
            if (!is_null($matchPlayerBan)) {
                $this->entityManager->persist($matchPlayerBan);
            }
        }

        $this->entityManager->flush();

        return $storedMatch;
    }

    /**
     * @param Player $player
     * @throws InvalidArgumentException
     */
    public function updatePlayerGods(Player $player): void
    {
        $playerGods = $this->smite->getPlayerGodDetails($player->getSmitePlayerId()) ?? [];

        if (!empty($playerGods)) {
            $godRepo = $this->entityManager->getRepository(God::class);
            $playerGodRepo = $this->entityManager->getRepository(PlayerGod::class);

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
            unset($playerGods);
        }
    }
}
