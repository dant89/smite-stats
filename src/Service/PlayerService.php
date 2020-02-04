<?php

namespace App\Service;

use App\Entity\God;
use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;
use App\Entity\Player;
use App\Entity\PlayerGod;
use App\Mapper\MatchPlayerAbilityMapper;
use App\Mapper\MatchPlayerBanMapper;
use App\Mapper\MatchPlayerItemMapper;
use App\Mapper\MatchPlayerMapper;
use App\Mapper\PlayerMapper;
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
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPlayerMatches(Player $player): array
    {
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);

        // Handle player matches
        $existingMatchIds = [];
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
        $recentMatchIds = array_slice($recentMatchIds, 0, 10, true);
        $matchDetails = $this->smite->getMatchDetailsBatch($recentMatchIds);

        // TODO store the latest matches, then query the database for matches by id for user - simple solution!

        $teams = [];

        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {

                // Search by the unique index constraint on PlayerMatch
                $storedMatch = $matchPlayerRepo->findOneBy([
                    'smiteMatchId' => $matchDetail['Match'],
                    'godId' => $matchDetail['GodId'],
                    'taskForce' => $matchDetail['TaskForce']
                ]);

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
                                $existingPlayer = $existingPlayerMapped;
                            }
                        }
                    }

                    $storedMatch = $this->matchPlayerMapper->from($matchDetail, $existingPlayer);
                    $this->entityManager->persist($storedMatch);
                    // Match has to be created before it can be used for sub entities
                    $this->entityManager->flush();

                    for ($i = 1; $i <= 6; $i++) {
                        $matchPlayerAbility = $this->matchPlayerAbilityMapper->from($matchDetail, $i, $storedMatch);
                        if (!is_null($matchPlayerAbility)) {
                            $this->entityManager->persist($matchPlayerAbility);
                        }
                    }

                    for ($i = 1; $i <= 4; $i++) {
                        $matchPlayerItem = $this->matchPlayerItemMapper->from($matchDetail, $i, $storedMatch);
                        if (!is_null($matchPlayerItem)) {
                            $this->entityManager->persist($matchPlayerItem);
                        }
                    }

                    for ($i = 1; $i <= 10; $i++) {
                        $matchPlayerBan = $this->matchPlayerBanMapper->from($matchDetail, $i, $storedMatch);
                        if (!is_null($matchPlayerBan)) {
                            $this->entityManager->persist($matchPlayerBan);
                        }
                    }

                    $this->entityManager->flush();
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
                        if ($smitePlayerId === $player->getSmitePlayerId()) {
                            $winner = true;
                        }
                    }
                }

                $formattedMatch['Player_Won'] = $winner;
            }
        }

        return $formattedMatches;
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
        }
    }
}
