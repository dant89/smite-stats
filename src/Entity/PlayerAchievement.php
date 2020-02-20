<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PlayerAchievement
 * @package App\Entity
 *
 * @ORM\Table(name="player_achievement", uniqueConstraints={@ORM\UniqueConstraint(name="unique_player_achievement", columns={"smite_player_id"})})
 * @ORM\Entity()
 */
class PlayerAchievement
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
     * @var int
     * @ORM\Column(name="assists", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $assists;

    /**
    * @var int
    * @ORM\Column(name="assisted_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $assistedKills;

    /**
    * @var int
    * @ORM\Column(name="camps_cleared", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $campsCleared;

    /**
    * @var int
    * @ORM\Column(name="deaths", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $deaths;

    /**
    * @var int
    * @ORM\Column(name="divine_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $divineSpree;

    /**
    * @var int
    * @ORM\Column(name="double_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $doubleKills;

    /**
    * @var int
    * @ORM\Column(name="fire_giant_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $fireGiantKills;

    /**
    * @var int
    * @ORM\Column(name="first_bloods", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $firstBloods;

    /**
    * @var int
    * @ORM\Column(name="god_like_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $godLikeSpree;

    /**
    * @var int
    * @ORM\Column(name="gold_fury_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $goldFuryKills;

    /**
    * @var int
    * @ORM\Column(name="immortal_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $immortalSpree;

    /**
    * @var int
    * @ORM\Column(name="killing_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $killingSpree;

    /**
    * @var int
    * @ORM\Column(name="minion_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $minionKills;

    /**
    * @var int
    * @ORM\Column(name="penta_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $pentaKills;

    /**
    * @var int
    * @ORM\Column(name="phoenix_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $phoenixKills;

    /**
    * @var int
    * @ORM\Column(name="player_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $playerKills;

    /**
    * @var int
    * @ORM\Column(name="quadra_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $quadraKills;

    /**
    * @var int
    * @ORM\Column(name="rampage_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $rampageSpree;

    /**
    * @var int
    * @ORM\Column(name="shutdown_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $shutdownSpree;

    /**
    * @var int
    * @ORM\Column(name="siege_juggernaut_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $siegeJuggernautKills;

    /**
    * @var int
    * @ORM\Column(name="tower_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $towerKills;

    /**
    * @var int
    * @ORM\Column(name="triple_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $tripleKills;

    /**
    * @var int
    * @ORM\Column(name="unstoppable_spree", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $unstoppableSpree;

    /**
    * @var int
    * @ORM\Column(name="wild_juggernaut_kills", type="integer", nullable=false, options={"unsigned"=true})
    */
    private $wildJuggernautKills;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlayerAchievement
     */
    public function setId(int $id): PlayerAchievement
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
     * @return PlayerAchievement
     */
    public function setSmitePlayer($smitePlayer)
    {
        $this->smitePlayer = $smitePlayer;
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
     * @return PlayerAchievement
     */
    public function setAssists(int $assists): PlayerAchievement
    {
        $this->assists = $assists;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssistedKills(): int
    {
        return $this->assistedKills;
    }

    /**
     * @param int $assistedKills
     * @return PlayerAchievement
     */
    public function setAssistedKills(int $assistedKills): PlayerAchievement
    {
        $this->assistedKills = $assistedKills;
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
     * @return PlayerAchievement
     */
    public function setCampsCleared(int $campsCleared): PlayerAchievement
    {
        $this->campsCleared = $campsCleared;
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
     * @return PlayerAchievement
     */
    public function setDeaths(int $deaths): PlayerAchievement
    {
        $this->deaths = $deaths;
        return $this;
    }

    /**
     * @return int
     */
    public function getDivineSpree(): int
    {
        return $this->divineSpree;
    }

    /**
     * @param int $divineSpree
     * @return PlayerAchievement
     */
    public function setDivineSpree(int $divineSpree): PlayerAchievement
    {
        $this->divineSpree = $divineSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoubleKills(): int
    {
        return $this->doubleKills;
    }

    /**
     * @param int $doubleKills
     * @return PlayerAchievement
     */
    public function setDoubleKills(int $doubleKills): PlayerAchievement
    {
        $this->doubleKills = $doubleKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getFireGiantKills(): int
    {
        return $this->fireGiantKills;
    }

    /**
     * @param int $fireGiantKills
     * @return PlayerAchievement
     */
    public function setFireGiantKills(int $fireGiantKills): PlayerAchievement
    {
        $this->fireGiantKills = $fireGiantKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstBloods(): int
    {
        return $this->firstBloods;
    }

    /**
     * @param int $firstBloods
     * @return PlayerAchievement
     */
    public function setFirstBloods(int $firstBloods): PlayerAchievement
    {
        $this->firstBloods = $firstBloods;
        return $this;
    }

    /**
     * @return int
     */
    public function getGodLikeSpree(): int
    {
        return $this->godLikeSpree;
    }

    /**
     * @param int $godLikeSpree
     * @return PlayerAchievement
     */
    public function setGodLikeSpree(int $godLikeSpree): PlayerAchievement
    {
        $this->godLikeSpree = $godLikeSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getGoldFuryKills(): int
    {
        return $this->goldFuryKills;
    }

    /**
     * @param int $goldFuryKills
     * @return PlayerAchievement
     */
    public function setGoldFuryKills(int $goldFuryKills): PlayerAchievement
    {
        $this->goldFuryKills = $goldFuryKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getImmortalSpree(): int
    {
        return $this->immortalSpree;
    }

    /**
     * @param int $immortalSpree
     * @return PlayerAchievement
     */
    public function setImmortalSpree(int $immortalSpree): PlayerAchievement
    {
        $this->immortalSpree = $immortalSpree;
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
     * @return PlayerAchievement
     */
    public function setKillingSpree(int $killingSpree): PlayerAchievement
    {
        $this->killingSpree = $killingSpree;
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
     * @return PlayerAchievement
     */
    public function setMinionKills(int $minionKills): PlayerAchievement
    {
        $this->minionKills = $minionKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getPentaKills(): int
    {
        return $this->pentaKills;
    }

    /**
     * @param int $pentaKills
     * @return PlayerAchievement
     */
    public function setPentaKills(int $pentaKills): PlayerAchievement
    {
        $this->pentaKills = $pentaKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getPhoenixKills(): int
    {
        return $this->phoenixKills;
    }

    /**
     * @param int $phoenixKills
     * @return PlayerAchievement
     */
    public function setPhoenixKills(int $phoenixKills): PlayerAchievement
    {
        $this->phoenixKills = $phoenixKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerKills(): int
    {
        return $this->playerKills;
    }

    /**
     * @param int $playerKills
     * @return PlayerAchievement
     */
    public function setPlayerKills(int $playerKills): PlayerAchievement
    {
        $this->playerKills = $playerKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuadraKills(): int
    {
        return $this->quadraKills;
    }

    /**
     * @param int $quadraKills
     * @return PlayerAchievement
     */
    public function setQuadraKills(int $quadraKills): PlayerAchievement
    {
        $this->quadraKills = $quadraKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getRampageSpree(): int
    {
        return $this->rampageSpree;
    }

    /**
     * @param int $rampageSpree
     * @return PlayerAchievement
     */
    public function setRampageSpree(int $rampageSpree): PlayerAchievement
    {
        $this->rampageSpree = $rampageSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getShutdownSpree(): int
    {
        return $this->shutdownSpree;
    }

    /**
     * @param int $shutdownSpree
     * @return PlayerAchievement
     */
    public function setShutdownSpree(int $shutdownSpree): PlayerAchievement
    {
        $this->shutdownSpree = $shutdownSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getSiegeJuggernautKills(): int
    {
        return $this->siegeJuggernautKills;
    }

    /**
     * @param int $siegeJuggernautKills
     * @return PlayerAchievement
     */
    public function setSiegeJuggernautKills(int $siegeJuggernautKills): PlayerAchievement
    {
        $this->siegeJuggernautKills = $siegeJuggernautKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getTowerKills(): int
    {
        return $this->towerKills;
    }

    /**
     * @param int $towerKills
     * @return PlayerAchievement
     */
    public function setTowerKills(int $towerKills): PlayerAchievement
    {
        $this->towerKills = $towerKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getTripleKills(): int
    {
        return $this->tripleKills;
    }

    /**
     * @param int $tripleKills
     * @return PlayerAchievement
     */
    public function setTripleKills(int $tripleKills): PlayerAchievement
    {
        $this->tripleKills = $tripleKills;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnstoppableSpree(): int
    {
        return $this->unstoppableSpree;
    }

    /**
     * @param int $unstoppableSpree
     * @return PlayerAchievement
     */
    public function setUnstoppableSpree(int $unstoppableSpree): PlayerAchievement
    {
        $this->unstoppableSpree = $unstoppableSpree;
        return $this;
    }

    /**
     * @return int
     */
    public function getWildJuggernautKills(): int
    {
        return $this->wildJuggernautKills;
    }

    /**
     * @param int $wildJuggernautKills
     * @return PlayerAchievement
     */
    public function setWildJuggernautKills(int $wildJuggernautKills): PlayerAchievement
    {
        $this->wildJuggernautKills = $wildJuggernautKills;
        return $this;
    }
}
