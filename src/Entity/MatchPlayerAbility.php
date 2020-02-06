<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_player_ability", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_player_ability", columns={"ability_id", "ability_number", "match_player_id"})})
 * @ORM\Entity
 */
class MatchPlayerAbility
{
    /**
     * @ORM\ManyToOne(targetEntity="MatchPlayer", inversedBy="abilities")
     * @ORM\JoinColumn(name="match_player_id", referencedColumnName="id")
     */
    private $matchPlayer;

    /**
     * @ORM\ManyToOne(targetEntity="MatchItem", inversedBy="matchPlayerAbilities")
     * @ORM\JoinColumn(name="ability_id", referencedColumnName="item_id")
     */
    private $ability;

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
     * @ORM\Column(name="ability_number", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $abilityNumber;

    /**
     * @return mixed
     */
    public function getMatchPlayer()
    {
        return $this->matchPlayer;
    }

    /**
     * @return mixed
     */
    public function getAbility()
    {
        return $this->ability;
    }

    /**
     * @param mixed $ability
     * @return MatchPlayerAbility
     */
    public function setAbility($ability)
    {
        $this->ability = $ability;
        return $this;
    }

    /**
     * @param mixed $matchPlayer
     * @return MatchPlayerAbility
     */
    public function setMatchPlayer($matchPlayer)
    {
        $this->matchPlayer = $matchPlayer;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MatchPlayerAbility
     */
    public function setId(int $id): MatchPlayerAbility
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbilityNumber(): int
    {
        return $this->abilityNumber;
    }

    /**
     * @param int $abilityNumber
     * @return MatchPlayerAbility
     */
    public function setAbilityNumber(int $abilityNumber): MatchPlayerAbility
    {
        $this->abilityNumber = $abilityNumber;
        return $this;
    }
}
