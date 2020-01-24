<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="player_god", uniqueConstraints={@ORM\UniqueConstraint(name="unique_player_god", columns={"smite_player_id", "god_id"})})
 * @ORM\Entity
 */
class PlayerGod
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="matches")
     * @ORM\JoinColumn(name="smite_player_id", referencedColumnName="smite_player_id", nullable=true)
     */
    private $smitePlayer;

    /**
     * @ORM\ManyToOne(targetEntity="God", inversedBy="abilities")
     * @ORM\JoinColumn(name="god_id", referencedColumnName="id")
     */
    private $god;

    /**
     * @var int
     *
     * @ORM\Column(name="assists", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $assists;

    /**
     * @var int
     *
     * @ORM\Column(name="deaths", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $deaths;

    /**
     * @var int
     *
     * @ORM\Column(name="kills", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $kills;

    /**
     * @var int
     *
     * @ORM\Column(name="losses", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $losses;

    /**
     * @var int
     *
     * @ORM\Column(name="minion_kills", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $minionKills;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $rank;

    /**
     * @var int
     *
     * @ORM\Column(name="wins", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $wins;

    /**
     * @var int
     *
     * @ORM\Column(name="worshippers", type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $worshippers;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlayerGod
     */
    public function setId(int $id): PlayerGod
    {
        $this->id = $id;
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
     * @return PlayerGod
     */
    public function setSmitePlayer($smitePlayer)
    {
        $this->smitePlayer = $smitePlayer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGod()
    {
        return $this->god;
    }

    /**
     * @param mixed $god
     * @return PlayerGod
     */
    public function setGod($god)
    {
        $this->god = $god;
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
     * @return PlayerGod
     */
    public function setAssists(int $assists): PlayerGod
    {
        $this->assists = $assists;
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
     * @return PlayerGod
     */
    public function setDeaths(int $deaths): PlayerGod
    {
        $this->deaths = $deaths;
        return $this;
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->kills;
    }

    /**
     * @param int $kills
     * @return PlayerGod
     */
    public function setKills(int $kills): PlayerGod
    {
        $this->kills = $kills;
        return $this;
    }

    /**
     * @return int
     */
    public function getLosses(): int
    {
        return $this->losses;
    }

    /**
     * @param int $losses
     * @return PlayerGod
     */
    public function setLosses(int $losses): PlayerGod
    {
        $this->losses = $losses;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinionKills(): int
    {
        return $this->minionKills;
    }

    /**
     * @param int $minionKills
     * @return PlayerGod
     */
    public function setMinionKills(int $minionKills): PlayerGod
    {
        $this->minionKills = $minionKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return PlayerGod
     */
    public function setRank(int $rank): PlayerGod
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * @return int
     */
    public function getWins(): int
    {
        return $this->wins;
    }

    /**
     * @param int $wins
     * @return PlayerGod
     */
    public function setWins(int $wins): PlayerGod
    {
        $this->wins = $wins;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorshippers(): int
    {
        return $this->worshippers;
    }

    /**
     * @param int $worshippers
     * @return PlayerGod
     */
    public function setWorshippers(int $worshippers): PlayerGod
    {
        $this->worshippers = $worshippers;
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
     * @return PlayerGod
     */
    public function setDateCreated(\DateTime $dateCreated): PlayerGod
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
     * @return PlayerGod
     */
    public function setDateUpdated(\DateTime $dateUpdated): PlayerGod
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }
}
