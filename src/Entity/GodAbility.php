<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GodAbility
 *
 * @ORM\Table(name="god_ability", uniqueConstraints={@ORM\UniqueConstraint(name="unique_ability_id", columns={"ability_id"})})
 * @ORM\Entity
 */
class GodAbility
{
    /**
     * @ORM\ManyToOne(targetEntity="God", inversedBy="abilities")
     * @ORM\JoinColumn(name="god_id", referencedColumnName="id")
     */
    private $god;

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
     * @ORM\Column(name="ability_number", type="integer", length=10, nullable=false, options={"unsigned"=true})
     */
    private $abilityNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="ability_id", type="integer", length=10, nullable=false, options={"unsigned"=true})
     */
    private $abilityId;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="cooldown", type="text", nullable=true)
     */
    private $cooldown;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="text", nullable=true)
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GodAbility
     */
    public function setId(int $id): GodAbility
    {
        $this->id = $id;
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
     * @return GodAbility
     */
    public function setGod($god)
    {
        $this->god = $god;
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
     * @return GodAbility
     */
    public function setAbilityNumber(int $abilityNumber): GodAbility
    {
        $this->abilityNumber = $abilityNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbilityId(): int
    {
        return $this->abilityId;
    }

    /**
     * @param int $abilityId
     * @return GodAbility
     */
    public function setAbilityId(int $abilityId): GodAbility
    {
        $this->abilityId = $abilityId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return GodAbility
     */
    public function setSummary(string $summary): GodAbility
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return GodAbility
     */
    public function setUrl(string $url): GodAbility
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getCooldown(): string
    {
        return $this->cooldown;
    }

    /**
     * @param string $cooldown
     * @return GodAbility
     */
    public function setCooldown(string $cooldown): GodAbility
    {
        $this->cooldown = $cooldown;
        return $this;
    }

    /**
     * @return string
     */
    public function getCost(): string
    {
        return $this->cost;
    }

    /**
     * @param string $cost
     * @return GodAbility
     */
    public function setCost(string $cost): GodAbility
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return GodAbility
     */
    public function setDescription(string $description): GodAbility
    {
        $this->description = $description;
        return $this;
    }
}
