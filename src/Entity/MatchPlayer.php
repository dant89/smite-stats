<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchPlayer
 *
 * smite_match_id combined with god_id should be unique, smite_player_id is NULLABLE so it can't guarantee uniqueness
 *
 * @ORM\Table(name="match_player", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_player_id", columns={"smite_match_id", "task_force", "god_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MatchPlayerRepository")
 */
class MatchPlayer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="smite_match_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $smiteMatchId;

    /**
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="matches")
     * @ORM\JoinColumn(name="smite_player_id", referencedColumnName="smite_player_id", nullable=true)
     */
    private $smitePlayer;

    /**
     * @ORM\OneToMany(targetEntity="MatchPlayerAbility", mappedBy="matchPlayer")
     */
    private $abilities;

    /**
     * @ORM\OneToMany(targetEntity="MatchPlayerBan", mappedBy="matchPlayer")
     */
    private $bans;

    /**
     * @ORM\OneToMany(targetEntity="MatchPlayerItem", mappedBy="matchPlayer")
     */
    private $items;

    /**
     * @var int
     *
     * @ORM\Column(name="account_level", type="integer", nullable=true, options={"unsigned"=true, "default"=0})
     */
    private $accountLevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="assists", type="integer", nullable=true, options={"unsigned"=true, "default"=0})
     */
    private $assists = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="camps_cleared", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $campsCleared = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="conquest_losses", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $conquestLosses = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="conquest_points", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $conquestPoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="conquest_tier", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $conquestTier = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="conquest_wins", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $conquestWins = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_bot", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageBot = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_done_in_hand", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageDoneInHand = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_done_magical", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageDoneMagical = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_done_physical", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageDonePhysical = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_mitigated", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageMitigated = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_player", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damagePlayer = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_taken", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageTaken = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_taken_magical", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageTakenMagical = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage_taken_physical", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $damageTakenPhysical = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="deaths", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $deaths = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="distance_traveled", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $distanceTraveled = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="duel_losses", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $duelLosses = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="duel_points", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $duelPoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="duel_tier", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $duelTier = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="duel_wins", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $duelWins = 0;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="entry_datetime", type="datetime", nullable=true)
     */
    private $entryDatetime;

    /**
     * @var int
     *
     * @ORM\Column(name="final_match_level", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $finalMatchLevel = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_ban_side", type="string", length=255, nullable=true)
     */
    private $firstBanSide;

    /**
     * @var int
     *
     * @ORM\Column(name="god_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $godId;

    /**
     * @var int
     *
     * @ORM\Column(name="gold_earned", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $goldEarned = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gold_per_minute", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $goldPerMinute = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="healing", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $healing = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="healing_bot", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $healingBot = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="healing_player_self", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $healingPlayerSelf = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="joust_losses", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $joustLosses = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="joust_points", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $joustPoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="joust_tier", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $joustTier = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="joust_wins", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $joustWins = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="killing_spree", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killingSpree = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_bot", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsBot = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_double", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsDouble = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_fire_giant", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsFireGiant = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_first_blood", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsFirstBlood = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_gold_fury", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsGoldFury = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_penta", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsPenta = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_phoenix", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsPhoenix = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_player", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsPlayer = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_quadra", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsQuadra = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_siege_juggernaut", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsSiegeJuggernaut = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_single", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsSingle = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_triple", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsTriple = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="kills_wild_juggernaut", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $killsWildJuggernaut = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="map_game", type="string", length=255, nullable=true)
     */
    private $mapGame;

    /**
     * @var int
     *
     * @ORM\Column(name="mastery_level", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $masteryLevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="match_duration", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $matchDuration = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="minutes", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $minutes = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="multi_kill_max", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $multiKillMax = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="objective_assists", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $objectiveAssists = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="party_id", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $partyId = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference_name", type="string", length=255, nullable=true)
     */
    private $referenceName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skin", type="string", length=255, nullable=true)
     */
    private $skin;

    /**
     * @var int
     *
     * @ORM\Column(name="skin_id", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $skinId = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="structure_damage", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $structureDamage = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="surrendered", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $surrendered = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="task_force", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $taskForce = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="team1score", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $team1Score = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="team2score", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $team2Score = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $teamId = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="team_name", type="string", length=255, nullable=true)
     */
    private $teamName;

    /**
     * @var int
     *
     * @ORM\Column(name="time_in_match_seconds", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $timeInMatchSeconds = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="towers_destroyed", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $towersDestroyed = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="wards_placed", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $wardsPlaced = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="win_status", type="string", length=255, nullable=true)
     */
    private $winStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="winning_task_force", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $winningTaskForce = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="match_queue_id", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $match_queue_id = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */
    private $dateUpdated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="crawled", type="integer", nullable=false, options={"default":0})
     */
    private $crawled = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MatchPlayer
     */
    public function setId(int $id): MatchPlayer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSmiteMatchId(): int
    {
        return $this->smiteMatchId;
    }

    /**
     * @param int $smiteMatchId
     * @return MatchPlayer
     */
    public function setSmiteMatchId(int $smiteMatchId): MatchPlayer
    {
        $this->smiteMatchId = $smiteMatchId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmitePlayer()
    {
        return $this->smitePlayer;
    }

    /**
     * @param mixed $smitePlayer
     * @return MatchPlayer
     */
    public function setSmitePlayer($smitePlayer)
    {
        $this->smitePlayer = $smitePlayer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAbilities()
    {
        return $this->abilities;
    }

    /**
     * @param mixed $abilities
     * @return MatchPlayer
     */
    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBans()
    {
        return $this->bans;
    }

    /**
     * @param mixed $bans
     * @return MatchPlayer
     */
    public function setBans($bans)
    {
        $this->bans = $bans;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     * @return MatchPlayer
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountLevel(): int
    {
        return $this->accountLevel;
    }

    /**
     * @param int $accountLevel
     * @return MatchPlayer
     */
    public function setAccountLevel(int $accountLevel): MatchPlayer
    {
        $this->accountLevel = $accountLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssists(): int
    {
        return $this->assists;
    }

    /**
     * @param int $assists
     * @return MatchPlayer
     */
    public function setAssists(int $assists): MatchPlayer
    {
        $this->assists = $assists;
        return $this;
    }

    /**
     * @return int
     */
    public function getCampsCleared(): int
    {
        return $this->campsCleared;
    }

    /**
     * @param int $campsCleared
     * @return MatchPlayer
     */
    public function setCampsCleared(int $campsCleared): MatchPlayer
    {
        $this->campsCleared = $campsCleared;
        return $this;
    }

    /**
     * @return int
     */
    public function getConquestLosses(): int
    {
        return $this->conquestLosses;
    }

    /**
     * @param int $conquestLosses
     * @return MatchPlayer
     */
    public function setConquestLosses(int $conquestLosses): MatchPlayer
    {
        $this->conquestLosses = $conquestLosses;
        return $this;
    }

    /**
     * @return int
     */
    public function getConquestPoints(): int
    {
        return $this->conquestPoints;
    }

    /**
     * @param int $conquestPoints
     * @return MatchPlayer
     */
    public function setConquestPoints(int $conquestPoints): MatchPlayer
    {
        $this->conquestPoints = $conquestPoints;
        return $this;
    }

    /**
     * @return int
     */
    public function getConquestTier(): int
    {
        return $this->conquestTier;
    }

    /**
     * @param int $conquestTier
     * @return MatchPlayer
     */
    public function setConquestTier(int $conquestTier): MatchPlayer
    {
        $this->conquestTier = $conquestTier;
        return $this;
    }

    /**
     * @return int
     */
    public function getConquestWins(): int
    {
        return $this->conquestWins;
    }

    /**
     * @param int $conquestWins
     * @return MatchPlayer
     */
    public function setConquestWins(int $conquestWins): MatchPlayer
    {
        $this->conquestWins = $conquestWins;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageBot(): int
    {
        return $this->damageBot;
    }

    /**
     * @param int $damageBot
     * @return MatchPlayer
     */
    public function setDamageBot(int $damageBot): MatchPlayer
    {
        $this->damageBot = $damageBot;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageDoneInHand(): int
    {
        return $this->damageDoneInHand;
    }

    /**
     * @param int $damageDoneInHand
     * @return MatchPlayer
     */
    public function setDamageDoneInHand(int $damageDoneInHand): MatchPlayer
    {
        $this->damageDoneInHand = $damageDoneInHand;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageDoneMagical(): int
    {
        return $this->damageDoneMagical;
    }

    /**
     * @param int $damageDoneMagical
     * @return MatchPlayer
     */
    public function setDamageDoneMagical(int $damageDoneMagical): MatchPlayer
    {
        $this->damageDoneMagical = $damageDoneMagical;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageDonePhysical(): int
    {
        return $this->damageDonePhysical;
    }

    /**
     * @param int $damageDonePhysical
     * @return MatchPlayer
     */
    public function setDamageDonePhysical(int $damageDonePhysical): MatchPlayer
    {
        $this->damageDonePhysical = $damageDonePhysical;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageMitigated(): int
    {
        return $this->damageMitigated;
    }

    /**
     * @param int $damageMitigated
     * @return MatchPlayer
     */
    public function setDamageMitigated(int $damageMitigated): MatchPlayer
    {
        $this->damageMitigated = $damageMitigated;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamagePlayer(): int
    {
        return $this->damagePlayer;
    }

    /**
     * @param int $damagePlayer
     * @return MatchPlayer
     */
    public function setDamagePlayer(int $damagePlayer): MatchPlayer
    {
        $this->damagePlayer = $damagePlayer;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageTaken(): int
    {
        return $this->damageTaken;
    }

    /**
     * @param int $damageTaken
     * @return MatchPlayer
     */
    public function setDamageTaken(int $damageTaken): MatchPlayer
    {
        $this->damageTaken = $damageTaken;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageTakenMagical(): int
    {
        return $this->damageTakenMagical;
    }

    /**
     * @param int $damageTakenMagical
     * @return MatchPlayer
     */
    public function setDamageTakenMagical(int $damageTakenMagical): MatchPlayer
    {
        $this->damageTakenMagical = $damageTakenMagical;
        return $this;
    }

    /**
     * @return int
     */
    public function getDamageTakenPhysical(): int
    {
        return $this->damageTakenPhysical;
    }

    /**
     * @param int $damageTakenPhysical
     * @return MatchPlayer
     */
    public function setDamageTakenPhysical(int $damageTakenPhysical): MatchPlayer
    {
        $this->damageTakenPhysical = $damageTakenPhysical;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return $this->deaths;
    }

    /**
     * @param int $deaths
     * @return MatchPlayer
     */
    public function setDeaths(int $deaths): MatchPlayer
    {
        $this->deaths = $deaths;
        return $this;
    }

    /**
     * @return int
     */
    public function getDistanceTraveled(): int
    {
        return $this->distanceTraveled;
    }

    /**
     * @param int $distanceTraveled
     * @return MatchPlayer
     */
    public function setDistanceTraveled(int $distanceTraveled): MatchPlayer
    {
        $this->distanceTraveled = $distanceTraveled;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuelLosses(): int
    {
        return $this->duelLosses;
    }

    /**
     * @param int $duelLosses
     * @return MatchPlayer
     */
    public function setDuelLosses(int $duelLosses): MatchPlayer
    {
        $this->duelLosses = $duelLosses;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuelPoints(): int
    {
        return $this->duelPoints;
    }

    /**
     * @param int $duelPoints
     * @return MatchPlayer
     */
    public function setDuelPoints(int $duelPoints): MatchPlayer
    {
        $this->duelPoints = $duelPoints;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuelTier(): int
    {
        return $this->duelTier;
    }

    /**
     * @param int $duelTier
     * @return MatchPlayer
     */
    public function setDuelTier(int $duelTier): MatchPlayer
    {
        $this->duelTier = $duelTier;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuelWins(): int
    {
        return $this->duelWins;
    }

    /**
     * @param int $duelWins
     * @return MatchPlayer
     */
    public function setDuelWins(int $duelWins): MatchPlayer
    {
        $this->duelWins = $duelWins;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEntryDatetime(): ?\DateTime
    {
        return $this->entryDatetime;
    }

    /**
     * @param \DateTime|null $entryDatetime
     * @return MatchPlayer
     */
    public function setEntryDatetime(?\DateTime $entryDatetime): MatchPlayer
    {
        $this->entryDatetime = $entryDatetime;
        return $this;
    }

    /**
     * @return int
     */
    public function getFinalMatchLevel(): int
    {
        return $this->finalMatchLevel;
    }

    /**
     * @param int $finalMatchLevel
     * @return MatchPlayer
     */
    public function setFinalMatchLevel(int $finalMatchLevel): MatchPlayer
    {
        $this->finalMatchLevel = $finalMatchLevel;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstBanSide(): ?string
    {
        return $this->firstBanSide;
    }

    /**
     * @param string|null $firstBanSide
     * @return MatchPlayer
     */
    public function setFirstBanSide(?string $firstBanSide): MatchPlayer
    {
        $this->firstBanSide = $firstBanSide;
        return $this;
    }

    /**
     * @return int
     */
    public function getGodId(): int
    {
        return $this->godId;
    }

    /**
     * @param int $godId
     * @return MatchPlayer
     */
    public function setGodId(int $godId): MatchPlayer
    {
        $this->godId = $godId;
        return $this;
    }

    /**
     * @return int
     */
    public function getGoldEarned(): int
    {
        return $this->goldEarned;
    }

    /**
     * @param int $goldEarned
     * @return MatchPlayer
     */
    public function setGoldEarned(int $goldEarned): MatchPlayer
    {
        $this->goldEarned = $goldEarned;
        return $this;
    }

    /**
     * @return int
     */
    public function getGoldPerMinute(): int
    {
        return $this->goldPerMinute;
    }

    /**
     * @param int $goldPerMinute
     * @return MatchPlayer
     */
    public function setGoldPerMinute(int $goldPerMinute): MatchPlayer
    {
        $this->goldPerMinute = $goldPerMinute;
        return $this;
    }

    /**
     * @return int
     */
    public function getHealing(): int
    {
        return $this->healing;
    }

    /**
     * @param int $healing
     * @return MatchPlayer
     */
    public function setHealing(int $healing): MatchPlayer
    {
        $this->healing = $healing;
        return $this;
    }

    /**
     * @return int
     */
    public function getHealingBot(): int
    {
        return $this->healingBot;
    }

    /**
     * @param int $healingBot
     * @return MatchPlayer
     */
    public function setHealingBot(int $healingBot): MatchPlayer
    {
        $this->healingBot = $healingBot;
        return $this;
    }

    /**
     * @return int
     */
    public function getHealingPlayerSelf(): int
    {
        return $this->healingPlayerSelf;
    }

    /**
     * @param int $healingPlayerSelf
     * @return MatchPlayer
     */
    public function setHealingPlayerSelf(int $healingPlayerSelf): MatchPlayer
    {
        $this->healingPlayerSelf = $healingPlayerSelf;
        return $this;
    }

    /**
     * @return int
     */
    public function getJoustLosses(): int
    {
        return $this->joustLosses;
    }

    /**
     * @param int $joustLosses
     * @return MatchPlayer
     */
    public function setJoustLosses(int $joustLosses): MatchPlayer
    {
        $this->joustLosses = $joustLosses;
        return $this;
    }

    /**
     * @return int
     */
    public function getJoustPoints(): int
    {
        return $this->joustPoints;
    }

    /**
     * @param int $joustPoints
     * @return MatchPlayer
     */
    public function setJoustPoints(int $joustPoints): MatchPlayer
    {
        $this->joustPoints = $joustPoints;
        return $this;
    }

    /**
     * @return int
     */
    public function getJoustTier(): int
    {
        return $this->joustTier;
    }

    /**
     * @param int $joustTier
     * @return MatchPlayer
     */
    public function setJoustTier(int $joustTier): MatchPlayer
    {
        $this->joustTier = $joustTier;
        return $this;
    }

    /**
     * @return int
     */
    public function getJoustWins(): int
    {
        return $this->joustWins;
    }

    /**
     * @param int $joustWins
     * @return MatchPlayer
     */
    public function setJoustWins(int $joustWins): MatchPlayer
    {
        $this->joustWins = $joustWins;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillingSpree(): int
    {
        return $this->killingSpree;
    }

    /**
     * @param int $killingSpree
     * @return MatchPlayer
     */
    public function setKillingSpree(int $killingSpree): MatchPlayer
    {
        $this->killingSpree = $killingSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsBot(): int
    {
        return $this->killsBot;
    }

    /**
     * @param int $killsBot
     * @return MatchPlayer
     */
    public function setKillsBot(int $killsBot): MatchPlayer
    {
        $this->killsBot = $killsBot;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsDouble(): int
    {
        return $this->killsDouble;
    }

    /**
     * @param int $killsDouble
     * @return MatchPlayer
     */
    public function setKillsDouble(int $killsDouble): MatchPlayer
    {
        $this->killsDouble = $killsDouble;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsFireGiant(): int
    {
        return $this->killsFireGiant;
    }

    /**
     * @param int $killsFireGiant
     * @return MatchPlayer
     */
    public function setKillsFireGiant(int $killsFireGiant): MatchPlayer
    {
        $this->killsFireGiant = $killsFireGiant;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsFirstBlood(): int
    {
        return $this->killsFirstBlood;
    }

    /**
     * @param int $killsFirstBlood
     * @return MatchPlayer
     */
    public function setKillsFirstBlood(int $killsFirstBlood): MatchPlayer
    {
        $this->killsFirstBlood = $killsFirstBlood;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsGoldFury(): int
    {
        return $this->killsGoldFury;
    }

    /**
     * @param int $killsGoldFury
     * @return MatchPlayer
     */
    public function setKillsGoldFury(int $killsGoldFury): MatchPlayer
    {
        $this->killsGoldFury = $killsGoldFury;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsPenta(): int
    {
        return $this->killsPenta;
    }

    /**
     * @param int $killsPenta
     * @return MatchPlayer
     */
    public function setKillsPenta(int $killsPenta): MatchPlayer
    {
        $this->killsPenta = $killsPenta;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsPhoenix(): int
    {
        return $this->killsPhoenix;
    }

    /**
     * @param int $killsPhoenix
     * @return MatchPlayer
     */
    public function setKillsPhoenix(int $killsPhoenix): MatchPlayer
    {
        $this->killsPhoenix = $killsPhoenix;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsPlayer(): int
    {
        return $this->killsPlayer;
    }

    /**
     * @param int $killsPlayer
     * @return MatchPlayer
     */
    public function setKillsPlayer(int $killsPlayer): MatchPlayer
    {
        $this->killsPlayer = $killsPlayer;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsQuadra(): int
    {
        return $this->killsQuadra;
    }

    /**
     * @param int $killsQuadra
     * @return MatchPlayer
     */
    public function setKillsQuadra(int $killsQuadra): MatchPlayer
    {
        $this->killsQuadra = $killsQuadra;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsSiegeJuggernaut(): int
    {
        return $this->killsSiegeJuggernaut;
    }

    /**
     * @param int $killsSiegeJuggernaut
     * @return MatchPlayer
     */
    public function setKillsSiegeJuggernaut(int $killsSiegeJuggernaut): MatchPlayer
    {
        $this->killsSiegeJuggernaut = $killsSiegeJuggernaut;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsSingle(): int
    {
        return $this->killsSingle;
    }

    /**
     * @param int $killsSingle
     * @return MatchPlayer
     */
    public function setKillsSingle(int $killsSingle): MatchPlayer
    {
        $this->killsSingle = $killsSingle;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsTriple(): int
    {
        return $this->killsTriple;
    }

    /**
     * @param int $killsTriple
     * @return MatchPlayer
     */
    public function setKillsTriple(int $killsTriple): MatchPlayer
    {
        $this->killsTriple = $killsTriple;
        return $this;
    }

    /**
     * @return int
     */
    public function getKillsWildJuggernaut(): int
    {
        return $this->killsWildJuggernaut;
    }

    /**
     * @param int $killsWildJuggernaut
     * @return MatchPlayer
     */
    public function setKillsWildJuggernaut(int $killsWildJuggernaut): MatchPlayer
    {
        $this->killsWildJuggernaut = $killsWildJuggernaut;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMapGame(): ?string
    {
        return $this->mapGame;
    }

    /**
     * @param string|null $mapGame
     * @return MatchPlayer
     */
    public function setMapGame(?string $mapGame): MatchPlayer
    {
        $this->mapGame = $mapGame;
        return $this;
    }

    /**
     * @return int
     */
    public function getMasteryLevel(): int
    {
        return $this->masteryLevel;
    }

    /**
     * @param int $masteryLevel
     * @return MatchPlayer
     */
    public function setMasteryLevel(int $masteryLevel): MatchPlayer
    {
        $this->masteryLevel = $masteryLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getMatchDuration(): int
    {
        return $this->matchDuration;
    }

    /**
     * @param int $matchDuration
     * @return MatchPlayer
     */
    public function setMatchDuration(int $matchDuration): MatchPlayer
    {
        $this->matchDuration = $matchDuration;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     * @return MatchPlayer
     */
    public function setMinutes(int $minutes): MatchPlayer
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return int
     */
    public function getMultiKillMax(): int
    {
        return $this->multiKillMax;
    }

    /**
     * @param int $multiKillMax
     * @return MatchPlayer
     */
    public function setMultiKillMax(int $multiKillMax): MatchPlayer
    {
        $this->multiKillMax = $multiKillMax;
        return $this;
    }

    /**
     * @return int
     */
    public function getObjectiveAssists(): int
    {
        return $this->objectiveAssists;
    }

    /**
     * @param int $objectiveAssists
     * @return MatchPlayer
     */
    public function setObjectiveAssists(int $objectiveAssists): MatchPlayer
    {
        $this->objectiveAssists = $objectiveAssists;
        return $this;
    }

    /**
     * @return int
     */
    public function getPartyId(): int
    {
        return $this->partyId;
    }

    /**
     * @param int $partyId
     * @return MatchPlayer
     */
    public function setPartyId(int $partyId): MatchPlayer
    {
        $this->partyId = $partyId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReferenceName(): ?string
    {
        return $this->referenceName;
    }

    /**
     * @param string|null $referenceName
     * @return MatchPlayer
     */
    public function setReferenceName(?string $referenceName): MatchPlayer
    {
        $this->referenceName = $referenceName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return MatchPlayer
     */
    public function setRegion(?string $region): MatchPlayer
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSkin(): ?string
    {
        return $this->skin;
    }

    /**
     * @param string|null $skin
     * @return MatchPlayer
     */
    public function setSkin(?string $skin): MatchPlayer
    {
        $this->skin = $skin;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkinId(): int
    {
        return $this->skinId;
    }

    /**
     * @param int $skinId
     * @return MatchPlayer
     */
    public function setSkinId(int $skinId): MatchPlayer
    {
        $this->skinId = $skinId;
        return $this;
    }

    /**
     * @return int
     */
    public function getStructureDamage(): int
    {
        return $this->structureDamage;
    }

    /**
     * @param int $structureDamage
     * @return MatchPlayer
     */
    public function setStructureDamage(int $structureDamage): MatchPlayer
    {
        $this->structureDamage = $structureDamage;
        return $this;
    }

    /**
     * @return int
     */
    public function getSurrendered(): int
    {
        return $this->surrendered;
    }

    /**
     * @param int $surrendered
     * @return MatchPlayer
     */
    public function setSurrendered(int $surrendered): MatchPlayer
    {
        $this->surrendered = $surrendered;
        return $this;
    }

    /**
     * @return int
     */
    public function getTaskForce(): int
    {
        return $this->taskForce;
    }

    /**
     * @param int $taskForce
     * @return MatchPlayer
     */
    public function setTaskForce(int $taskForce): MatchPlayer
    {
        $this->taskForce = $taskForce;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeam1Score(): int
    {
        return $this->team1Score;
    }

    /**
     * @param int $team1Score
     * @return MatchPlayer
     */
    public function setTeam1Score(int $team1Score): MatchPlayer
    {
        $this->team1Score = $team1Score;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeam2Score(): int
    {
        return $this->team2Score;
    }

    /**
     * @param int $team2Score
     * @return MatchPlayer
     */
    public function setTeam2Score(int $team2Score): MatchPlayer
    {
        $this->team2Score = $team2Score;
        return $this;
    }

    /**
     * @return int
     */
    public function getTeamId(): int
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     * @return MatchPlayer
     */
    public function setTeamId(int $teamId): MatchPlayer
    {
        $this->teamId = $teamId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    /**
     * @param string|null $teamName
     * @return MatchPlayer
     */
    public function setTeamName(?string $teamName): MatchPlayer
    {
        $this->teamName = $teamName;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeInMatchSeconds(): int
    {
        return $this->timeInMatchSeconds;
    }

    /**
     * @param int $timeInMatchSeconds
     * @return MatchPlayer
     */
    public function setTimeInMatchSeconds(int $timeInMatchSeconds): MatchPlayer
    {
        $this->timeInMatchSeconds = $timeInMatchSeconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getTowersDestroyed(): int
    {
        return $this->towersDestroyed;
    }

    /**
     * @param int $towersDestroyed
     * @return MatchPlayer
     */
    public function setTowersDestroyed(int $towersDestroyed): MatchPlayer
    {
        $this->towersDestroyed = $towersDestroyed;
        return $this;
    }

    /**
     * @return int
     */
    public function getWardsPlaced(): int
    {
        return $this->wardsPlaced;
    }

    /**
     * @param int $wardsPlaced
     * @return MatchPlayer
     */
    public function setWardsPlaced(int $wardsPlaced): MatchPlayer
    {
        $this->wardsPlaced = $wardsPlaced;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWinStatus(): ?string
    {
        return $this->winStatus;
    }

    /**
     * @param string|null $winStatus
     * @return MatchPlayer
     */
    public function setWinStatus(?string $winStatus): MatchPlayer
    {
        $this->winStatus = $winStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getWinningTaskForce(): int
    {
        return $this->winningTaskForce;
    }

    /**
     * @param int $winningTaskForce
     * @return MatchPlayer
     */
    public function setWinningTaskForce(int $winningTaskForce): MatchPlayer
    {
        $this->winningTaskForce = $winningTaskForce;
        return $this;
    }

    /**
     * @return int
     */
    public function getMatchQueueId(): int
    {
        return $this->match_queue_id;
    }

    /**
     * @param int $match_queue_id
     * @return MatchPlayer
     */
    public function setMatchQueueId(int $match_queue_id): MatchPlayer
    {
        $this->match_queue_id = $match_queue_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return MatchPlayer
     */
    public function setName(?string $name): MatchPlayer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     * @return MatchPlayer
     */
    public function setDateCreated(\DateTime $dateCreated): MatchPlayer
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated(): \DateTime
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     * @return MatchPlayer
     */
    public function setDateUpdated(\DateTime $dateUpdated): MatchPlayer
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCrawled(): ?int
    {
        return $this->crawled;
    }

    /**
     * @param int|null $crawled
     * @return MatchPlayer
     */
    public function setCrawled(?int $crawled): MatchPlayer
    {
        $this->crawled = $crawled;
        return $this;
    }
}
