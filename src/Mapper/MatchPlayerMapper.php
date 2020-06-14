<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\Player;
use Psr\Log\LoggerInterface;

class MatchPlayerMapper
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function from(array $data, ?Player $player = null): MatchPlayer
    {
        try {
            $matchPlayer = new MatchPlayer();
            $matchPlayer->setSmiteMatchId($data['Match']);
            $matchPlayer->setName($data['name']);
            $matchPlayer->setAccountLevel($data['Account_Level']);
            $matchPlayer->setSmitePlayer($player);
            $matchPlayer->setAssists($data['Assists']);
            $matchPlayer->setCampsCleared($data['Camps_Cleared']);
            $matchPlayer->setConquestLosses($data['Conquest_Losses']);
            $matchPlayer->setConquestPoints($data['Conquest_Points']);
            $matchPlayer->setConquestTier($data['Conquest_Tier']);
            $matchPlayer->setConquestWins($data['Conquest_Wins']);
            $matchPlayer->setDamageBot($data['Damage_Bot']);
            $matchPlayer->setDamageDoneInHand($data['Damage_Done_In_Hand']);
            $matchPlayer->setDamageDoneMagical($data['Damage_Done_Magical']);
            $matchPlayer->setDamageDonePhysical($data['Damage_Done_Physical']);
            $matchPlayer->setDamageMitigated($data['Damage_Mitigated']);
            $matchPlayer->setDamagePlayer($data['Damage_Player']);
            $matchPlayer->setDamageTaken($data['Damage_Taken']);
            $matchPlayer->setDamageTakenMagical($data['Damage_Taken_Magical']);
            $matchPlayer->setDamageTakenPhysical($data['Damage_Taken_Physical']);
            $matchPlayer->setDamageTakenPhysical($data['Damage_Taken_Physical']);
            $matchPlayer->setDeaths($data['Deaths']);
            $matchPlayer->setDistanceTraveled($data['Distance_Traveled']);
            $matchPlayer->setDuelLosses($data['Duel_Losses']);
            $matchPlayer->setDuelPoints($data['Duel_Points']);
            $matchPlayer->setDuelTier($data['Duel_Tier']);
            $matchPlayer->setDuelWins($data['Duel_Wins']);
            $matchPlayer->setEntryDatetime(new \DateTime($data['Entry_Datetime']));
            $matchPlayer->setFinalMatchLevel($data['Final_Match_Level']);
            $matchPlayer->setFirstBanSide($data['First_Ban_Side']);
            $matchPlayer->setGodId($data['GodId']);
            $matchPlayer->setGoldEarned($data['Gold_Earned']);
            $matchPlayer->setGoldPerMinute($data['Gold_Per_Minute']);
            $matchPlayer->setHealing($data['Healing']);
            $matchPlayer->setHealingBot($data['Healing_Bot']);
            $matchPlayer->setHealingPlayerSelf($data['Healing_Player_Self']);
            $matchPlayer->setJoustLosses($data['Joust_Losses']);
            $matchPlayer->setJoustPoints($data['Joust_Points']);
            $matchPlayer->setJoustTier($data['Joust_Tier']);
            $matchPlayer->setJoustWins($data['Joust_Wins']);
            $matchPlayer->setKillingSpree($data['Killing_Spree']);
            $matchPlayer->setKillsBot($data['Kills_Bot']);
            $matchPlayer->setKillsDouble($data['Kills_Double']);
            $matchPlayer->setKillsFireGiant($data['Kills_Fire_Giant']);
            $matchPlayer->setKillsFirstBlood($data['Kills_First_Blood']);
            $matchPlayer->setKillsGoldFury($data['Kills_Gold_Fury']);
            $matchPlayer->setKillsPenta($data['Kills_Penta']);
            $matchPlayer->setKillsPhoenix($data['Kills_Phoenix']);
            $matchPlayer->setKillsPlayer($data['Kills_Player']);
            $matchPlayer->setKillsQuadra($data['Kills_Quadra']);
            $matchPlayer->setKillsSiegeJuggernaut($data['Kills_Siege_Juggernaut']);
            $matchPlayer->setKillsSingle($data['Kills_Single']);
            $matchPlayer->setKillsTriple($data['Kills_Triple']);
            $matchPlayer->setKillsWildJuggernaut($data['Kills_Wild_Juggernaut']);
            $matchPlayer->setMapGame($data['Map_Game']);
            $matchPlayer->setMasteryLevel($data['Mastery_Level']);
            $matchPlayer->setMatchDuration($data['Match_Duration']);
            $matchPlayer->setMinutes($data['Minutes']);
            $matchPlayer->setMultiKillMax($data['Multi_kill_Max']);
            $matchPlayer->setObjectiveAssists($data['Objective_Assists']);
            $matchPlayer->setPartyId($data['PartyId']);
            $matchPlayer->setReferenceName($data['Reference_Name']);
            $matchPlayer->setRegion($data['Region']);
            $matchPlayer->setSkin($data['Skin']);
            $matchPlayer->setSkinId($data['SkinId']);
            $matchPlayer->setStructureDamage($data['Structure_Damage']);
            $matchPlayer->setSurrendered($data['Surrendered']);
            $matchPlayer->setTaskForce($data['TaskForce']);
            $matchPlayer->setTeam1Score($data['Team1Score']);
            $matchPlayer->setTeam2Score($data['Team2Score']);
            $matchPlayer->setTeamId($data['TeamId']);
            $matchPlayer->setTeamName($data['Team_Name']);
            $matchPlayer->setTimeInMatchSeconds($data['Time_In_Match_Seconds']);
            $matchPlayer->setTowersDestroyed($data['Towers_Destroyed']);
            $matchPlayer->setWardsPlaced($data['Wards_Placed']);
            $matchPlayer->setWinStatus($data['Win_Status']);
            $matchPlayer->setWinningTaskForce($data['Winning_TaskForce']);
            $matchPlayer->setMatchQueueId($data['match_queue_id']);
            $matchPlayer->setDateCreated(new \DateTime());
            $matchPlayer->setDateUpdated(new \DateTime());
            $matchPlayer->setCrawled(1);
        } catch (\Exception $e) {
            $this->logger->error('Could not map data to match player.', [
                'exception' => $e,
                'match_player' => $data
            ]);
            throw new \RuntimeException('Could not map data to match player.');
        }

        return $matchPlayer;
    }
}
