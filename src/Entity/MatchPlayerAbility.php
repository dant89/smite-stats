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
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ability_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $abilityId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ability_name", type="string", length=255, nullable=true)
     */
    private $abilityName;

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
     * @return int|null
     */
    public function getAbilityId(): ?int
    {
        return $this->abilityId;
    }

    /**
     * @param int|null $abilityId
     * @return MatchPlayerAbility
     */
    public function setAbilityId(?int $abilityId): MatchPlayerAbility
    {
        $this->abilityId = $abilityId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbilityName(): ?string
    {
        return $this->abilityName;
    }

    /**
     * @param string|null $abilityName
     * @return MatchPlayerAbility
     */
    public function setAbilityName(?string $abilityName): MatchPlayerAbility
    {
        $this->abilityName = $abilityName;
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
