<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player
 *
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @var string
     *
     * @ORM\Column(name="smite_player_id", type="integer", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @Assert\GreaterThan(0)
     */
    private $smitePlayerId;

    /**
     * @ORM\OneToMany(targetEntity="MatchPlayer", mappedBy="smitePlayer")
     */
    private $matches;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar_url", type="string", length=255, nullable=true)
     */
    private $avatarUrl;

    /**
     * @var int|null
     *
     * @ORM\Column(name="hours_played", type="integer", nullable=false, options={"default":0})
     */
    private $hoursPlayed = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="leaves", type="integer", nullable=false, options={"default":0})
     */
    private $leaves = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="level", type="integer", nullable=false, options={"default":0})
     */
    private $level = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="losses", type="integer", nullable=false, options={"default":0})
     */
    private $losses = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mastery_level", type="integer", nullable=false, options={"default":0})
     */
    private $masteryLevel = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="personal_status_message", type="string", nullable=true)
     */
    private $personalStatusMessage;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank_stat_conquest", type="integer", nullable=false, options={"default":0})
     */
    private $rankStatConquest = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank_stat_duel", type="integer", nullable=false, options={"default":0})
     */
    private $rankStatDuel = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank_stat_joust", type="integer", nullable=false, options={"default":0})
     */
    private $rankStatJoust = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region", type="string", nullable=true)
     */
    private $region;

    /**
     * @var int|null
     *
     * @ORM\Column(name="team_id", type="integer", nullable=true)
     */
    private $teamId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="team_name", type="string", nullable=true)
     */
    private $teamName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tier_conquest", type="integer", nullable=false, options={"default":0})
     */
    private $tierConquest = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tier_duel", type="integer", nullable=false, options={"default":0})
     */
    private $tierDuel = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tier_joust", type="integer", nullable=false, options={"default":0})
     */
    private $tierJoust = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_achievements", type="integer", nullable=false, options={"default":0})
     */
    private $totalAchievements = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_worshippers", type="integer", nullable=false, options={"default":0})
     */
    private $totalWorshippers = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wins", type="integer", nullable=false, options={"default":0})
     */
    private $wins = 0;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_registered", type="datetime", nullable=true)
     */
    private $dateRegistered;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_last_login", type="datetime", nullable=true)
     */
    private $dateLastLogin;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="gods_date_updated", type="datetime", nullable=true)
     */
    private $godsDateUpdated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="crawled", type="integer", nullable=false, options={"default":0})
     */
    private $crawled = 0;

    /**
     * @return int
     */
    public function getSmitePlayerId(): int
    {
        return $this->smitePlayerId;
    }

    /**
     * @param int $smitePlayerId
     * @return Player
     */
    public function setSmitePlayerId(int $smitePlayerId): Player
    {
        $this->smitePlayerId = $smitePlayerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @param mixed $matches
     * @return Player
     */
    public function setMatches($matches)
    {
        $this->matches = $matches;
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
     * @return Player
     */
    public function setName(?string $name): Player
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    /**
     * @param string|null $avatarUrl
     * @return Player
     */
    public function setAvatarUrl(?string $avatarUrl): Player
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHoursPlayed(): ?int
    {
        return $this->hoursPlayed;
    }

    /**
     * @param int|null $hoursPlayed
     * @return Player
     */
    public function setHoursPlayed(?int $hoursPlayed): Player
    {
        $this->hoursPlayed = $hoursPlayed;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLeaves(): ?int
    {
        return $this->leaves;
    }

    /**
     * @param int|null $leaves
     * @return Player
     */
    public function setLeaves(?int $leaves): Player
    {
        $this->leaves = $leaves;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int|null $level
     * @return Player
     */
    public function setLevel(?int $level): Player
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLosses(): ?int
    {
        return $this->losses;
    }

    /**
     * @param int|null $losses
     * @return Player
     */
    public function setLosses(?int $losses): Player
    {
        $this->losses = $losses;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMasteryLevel(): ?int
    {
        return $this->masteryLevel;
    }

    /**
     * @param int|null $masteryLevel
     * @return Player
     */
    public function setMasteryLevel(?int $masteryLevel): Player
    {
        $this->masteryLevel = $masteryLevel;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPersonalStatusMessage(): ?string
    {
        return $this->personalStatusMessage;
    }

    /**
     * @param string|null $personalStatusMessage
     * @return Player
     */
    public function setPersonalStatusMessage(?string $personalStatusMessage): Player
    {
        $this->personalStatusMessage = $personalStatusMessage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRankStatConquest(): ?int
    {
        return $this->rankStatConquest;
    }

    /**
     * @param int|null $rankStatConquest
     * @return Player
     */
    public function setRankStatConquest(?int $rankStatConquest): Player
    {
        $this->rankStatConquest = $rankStatConquest;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRankStatDuel(): ?int
    {
        return $this->rankStatDuel;
    }

    /**
     * @param int|null $rankStatDuel
     * @return Player
     */
    public function setRankStatDuel(?int $rankStatDuel): Player
    {
        $this->rankStatDuel = $rankStatDuel;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRankStatJoust(): ?int
    {
        return $this->rankStatJoust;
    }

    /**
     * @param int|null $rankStatJoust
     * @return Player
     */
    public function setRankStatJoust(?int $rankStatJoust): Player
    {
        $this->rankStatJoust = $rankStatJoust;
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
     * @return Player
     */
    public function setRegion(?string $region): Player
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    /**
     * @param int|null $teamId
     * @return Player
     */
    public function setTeamId(?int $teamId): Player
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
     * @return Player
     */
    public function setTeamName(?string $teamName): Player
    {
        $this->teamName = $teamName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTierConquest(): ?int
    {
        return $this->tierConquest;
    }

    /**
     * @param int|null $tierConquest
     * @return Player
     */
    public function setTierConquest(?int $tierConquest): Player
    {
        $this->tierConquest = $tierConquest;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTierDuel(): ?int
    {
        return $this->tierDuel;
    }

    /**
     * @param int|null $tierDuel
     * @return Player
     */
    public function setTierDuel(?int $tierDuel): Player
    {
        $this->tierDuel = $tierDuel;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTierJoust(): ?int
    {
        return $this->tierJoust;
    }

    /**
     * @param int|null $tierJoust
     * @return Player
     */
    public function setTierJoust(?int $tierJoust): Player
    {
        $this->tierJoust = $tierJoust;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalAchievements(): ?int
    {
        return $this->totalAchievements;
    }

    /**
     * @param int|null $totalAchievements
     * @return Player
     */
    public function setTotalAchievements(?int $totalAchievements): Player
    {
        $this->totalAchievements = $totalAchievements;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalWorshippers(): ?int
    {
        return $this->totalWorshippers;
    }

    /**
     * @param int|null $totalWorshippers
     * @return Player
     */
    public function setTotalWorshippers(?int $totalWorshippers): Player
    {
        $this->totalWorshippers = $totalWorshippers;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWins(): ?int
    {
        return $this->wins;
    }

    /**
     * @param int|null $wins
     * @return Player
     */
    public function setWins(?int $wins): Player
    {
        $this->wins = $wins;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateRegistered(): ?\DateTime
    {
        return $this->dateRegistered;
    }

    /**
     * @param \DateTime|null $dateRegistered
     * @return Player
     */
    public function setDateRegistered(?\DateTime $dateRegistered): Player
    {
        $this->dateRegistered = $dateRegistered;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateLastLogin(): ?\DateTime
    {
        return $this->dateLastLogin;
    }

    /**
     * @param \DateTime|null $dateLastLogin
     * @return Player
     */
    public function setDateLastLogin(?\DateTime $dateLastLogin): Player
    {
        $this->dateLastLogin = $dateLastLogin;
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
     * @return Player
     */
    public function setDateCreated(\DateTime $dateCreated): Player
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
     * @return Player
     */
    public function setDateUpdated(\DateTime $dateUpdated): Player
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getGodsDateUpdated(): ?\DateTime
    {
        return $this->godsDateUpdated;
    }

    /**
     * @param \DateTime|null $godsDateUpdated
     * @return Player
     */
    public function setGodsDateUpdated(?\DateTime $godsDateUpdated): Player
    {
        $this->godsDateUpdated = $godsDateUpdated;
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
     * @return Player
     */
    public function setCrawled(?int $crawled): Player
    {
        $this->crawled = $crawled;
        return $this;
    }
}
