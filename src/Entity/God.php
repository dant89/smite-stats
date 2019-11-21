<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * God
 *
 * @ORM\Table(name="god", uniqueConstraints={@ORM\UniqueConstraint(name="unique_smite_id", columns={"smite_id"})})
 * @ORM\Entity
 */
class God
{
    /**
     * @ORM\OneToMany(targetEntity="GodAbility", mappedBy="god")
     */
    private $abilities;

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
     * @ORM\Column(name="smite_id", type="integer", nullable=true)
     */
    private $smiteId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var float|null
     *
     * @ORM\Column(name="attack_speed", type="float", length=10, nullable=true)
     */
    private $attackSpeed;

    /**
     * @var float|null
     *
     * @ORM\Column(name="attack_speed_per_level", type="float", length=10, nullable=true)
     */
    private $attackSpeedPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="hp5per_level", type="float", length=10, nullable=true)
     */
    private $hp5PerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="health", type="float", length=10, nullable=true)
     */
    private $health;

    /**
     * @var float|null
     *
     * @ORM\Column(name="health_per_five", type="float", length=10, nullable=true)
     */
    private $healthPerFive;

    /**
     * @var float|null
     *
     * @ORM\Column(name="health_per_level", type="float", length=10, nullable=true)
     */
    private $healthPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="mp5per_level", type="float", length=10, nullable=true)
     */
    private $mp5PerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="magic_protection", type="float", length=10, nullable=true)
     */
    private $magicProtection;

    /**
     * @var float|null
     *
     * @ORM\Column(name="magic_protection_per_level", type="float", length=10, nullable=true)
     */
    private $magicProtectionPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="magical_power", type="float", length=10, nullable=true)
     */
    private $magicalPower;

    /**
     * @var float|null
     *
     * @ORM\Column(name="magical_power_per_level", type="float", length=10, nullable=true)
     */
    private $magicalPowerPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="mana", type="float", length=10, nullable=true)
     */
    private $mana;

    /**
     * @var float|null
     *
     * @ORM\Column(name="mana_per_five", type="float", length=10, nullable=true)
     */
    private $manaPerFive;

    /**
     * @var float|null
     *
     * @ORM\Column(name="mana_per_level", type="float", length=10, nullable=true)
     */
    private $manaPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="physical_power", type="float", length=10, nullable=true)
     */
    private $physicalPower;

    /**
     * @var float|null
     *
     * @ORM\Column(name="physical_power_per_level", type="float", length=10, nullable=true)
     */
    private $physicalPowerPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="physical_protection", type="float", length=10, nullable=true)
     */
    private $physicalProtection;

    /**
     * @var float|null
     *
     * @ORM\Column(name="physical_protection_per_level", type="float", length=10, nullable=true)
     */
    private $physicalProtectionPerLevel;

    /**
     * @var float|null
     *
     * @ORM\Column(name="speed", type="float", length=10, nullable=true)
     */
    private $speed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pros", type="string", length=255, nullable=true)
     */
    private $pros;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cons", type="string", length=255, nullable=true)
     */
    private $cons;

    /**
     * @var string|null
     *
     * @ORM\Column(name="on_free_rotation", type="string", length=255, nullable=true)
     */
    private $onFreeRotation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pantheon", type="string", length=255, nullable=true)
     */
    private $pantheon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lore", type="text", nullable=true)
     */
    private $lore;

    /**
     * @var string|null
     *
     * @ORM\Column(name="card_url", type="string", length=255, nullable=true)
     */
    private $cardUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="icon_url", type="string", length=255, nullable=true)
     */
    private $iconUrl;

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

    public function __construct() {
        $this->abilities = new ArrayCollection();
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
     * @return God
     */
    public function setId(int $id): God
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSmiteId(): ?int
    {
        return $this->smiteId;
    }

    /**
     * @param int|null $smiteId
     * @return God
     */
    public function setSmiteId(?int $smiteId): God
    {
        $this->smiteId = $smiteId;
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
     * @return God
     */
    public function setName(?string $name): God
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAttackSpeed(): ?float
    {
        return $this->attackSpeed;
    }

    /**
     * @param float|null $attackSpeed
     * @return God
     */
    public function setAttackSpeed(?float $attackSpeed): God
    {
        $this->attackSpeed = $attackSpeed;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAttackSpeedPerLevel(): ?float
    {
        return $this->attackSpeedPerLevel;
    }

    /**
     * @param float|null $attackSpeedPerLevel
     * @return God
     */
    public function setAttackSpeedPerLevel(?float $attackSpeedPerLevel): God
    {
        $this->attackSpeedPerLevel = $attackSpeedPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHp5PerLevel(): ?float
    {
        return $this->hp5PerLevel;
    }

    /**
     * @param float|null $hp5PerLevel
     * @return God
     */
    public function setHp5PerLevel(?float $hp5PerLevel): God
    {
        $this->hp5PerLevel = $hp5PerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHealth(): ?float
    {
        return $this->health;
    }

    /**
     * @param float|null $health
     * @return God
     */
    public function setHealth(?float $health): God
    {
        $this->health = $health;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHealthPerFive(): ?float
    {
        return $this->healthPerFive;
    }

    /**
     * @param float|null $healthPerFive
     * @return God
     */
    public function setHealthPerFive(?float $healthPerFive): God
    {
        $this->healthPerFive = $healthPerFive;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHealthPerLevel(): ?float
    {
        return $this->healthPerLevel;
    }

    /**
     * @param float|null $healthPerLevel
     * @return God
     */
    public function setHealthPerLevel(?float $healthPerLevel): God
    {
        $this->healthPerLevel = $healthPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMp5PerLevel(): ?float
    {
        return $this->mp5PerLevel;
    }

    /**
     * @param float|null $mp5PerLevel
     * @return God
     */
    public function setMp5PerLevel(?float $mp5PerLevel): God
    {
        $this->mp5PerLevel = $mp5PerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMagicProtection(): ?float
    {
        return $this->magicProtection;
    }

    /**
     * @param float|null $magicProtection
     * @return God
     */
    public function setMagicProtection(?float $magicProtection): God
    {
        $this->magicProtection = $magicProtection;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMagicProtectionPerLevel(): ?float
    {
        return $this->magicProtectionPerLevel;
    }

    /**
     * @param float|null $magicProtectionPerLevel
     * @return God
     */
    public function setMagicProtectionPerLevel(?float $magicProtectionPerLevel): God
    {
        $this->magicProtectionPerLevel = $magicProtectionPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMagicalPower(): ?float
    {
        return $this->magicalPower;
    }

    /**
     * @param float|null $magicalPower
     * @return God
     */
    public function setMagicalPower(?float $magicalPower): God
    {
        $this->magicalPower = $magicalPower;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMagicalPowerPerLevel(): ?float
    {
        return $this->magicalPowerPerLevel;
    }

    /**
     * @param float|null $magicalPowerPerLevel
     * @return God
     */
    public function setMagicalPowerPerLevel(?float $magicalPowerPerLevel): God
    {
        $this->magicalPowerPerLevel = $magicalPowerPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMana(): ?float
    {
        return $this->mana;
    }

    /**
     * @param float|null $mana
     * @return God
     */
    public function setMana(?float $mana): God
    {
        $this->mana = $mana;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getManaPerFive(): ?float
    {
        return $this->manaPerFive;
    }

    /**
     * @param float|null $manaPerFive
     * @return God
     */
    public function setManaPerFive(?float $manaPerFive): God
    {
        $this->manaPerFive = $manaPerFive;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getManaPerLevel(): ?float
    {
        return $this->manaPerLevel;
    }

    /**
     * @param float|null $manaPerLevel
     * @return God
     */
    public function setManaPerLevel(?float $manaPerLevel): God
    {
        $this->manaPerLevel = $manaPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPhysicalPower(): ?float
    {
        return $this->physicalPower;
    }

    /**
     * @param float|null $physicalPower
     * @return God
     */
    public function setPhysicalPower(?float $physicalPower): God
    {
        $this->physicalPower = $physicalPower;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPhysicalPowerPerLevel(): ?float
    {
        return $this->physicalPowerPerLevel;
    }

    /**
     * @param float|null $physicalPowerPerLevel
     * @return God
     */
    public function setPhysicalPowerPerLevel(?float $physicalPowerPerLevel): God
    {
        $this->physicalPowerPerLevel = $physicalPowerPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPhysicalProtection(): ?float
    {
        return $this->physicalProtection;
    }

    /**
     * @param float|null $physicalProtection
     * @return God
     */
    public function setPhysicalProtection(?float $physicalProtection): God
    {
        $this->physicalProtection = $physicalProtection;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPhysicalProtectionPerLevel(): ?float
    {
        return $this->physicalProtectionPerLevel;
    }

    /**
     * @param float|null $physicalProtectionPerLevel
     * @return God
     */
    public function setPhysicalProtectionPerLevel(?float $physicalProtectionPerLevel): God
    {
        $this->physicalProtectionPerLevel = $physicalProtectionPerLevel;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    /**
     * @param float|null $speed
     * @return God
     */
    public function setSpeed(?float $speed): God
    {
        $this->speed = $speed;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPros(): ?string
    {
        return $this->pros;
    }

    /**
     * @param string|null $pros
     * @return God
     */
    public function setPros(?string $pros): God
    {
        $this->pros = $pros;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCons(): ?string
    {
        return $this->cons;
    }

    /**
     * @param string|null $cons
     * @return God
     */
    public function setCons(?string $cons): God
    {
        $this->cons = $cons;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOnFreeRotation(): ?string
    {
        return $this->onFreeRotation;
    }

    /**
     * @param string|null $onFreeRotation
     * @return God
     */
    public function setOnFreeRotation(?string $onFreeRotation): God
    {
        $this->onFreeRotation = $onFreeRotation;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPantheon(): ?string
    {
        return $this->pantheon;
    }

    /**
     * @param string|null $pantheon
     * @return God
     */
    public function setPantheon(?string $pantheon): God
    {
        $this->pantheon = $pantheon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoles(): ?string
    {
        return $this->roles;
    }

    /**
     * @param string|null $roles
     * @return God
     */
    public function setRoles(?string $roles): God
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return God
     */
    public function setTitle(?string $title): God
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return God
     */
    public function setType(?string $type): God
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLore(): ?string
    {
        return $this->lore;
    }

    /**
     * @param string|null $lore
     * @return God
     */
    public function setLore(?string $lore): God
    {
        $this->lore = $lore;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCardUrl(): ?string
    {
        return $this->cardUrl;
    }

    /**
     * @param string|null $cardUrl
     * @return God
     */
    public function setCardUrl(?string $cardUrl): God
    {
        $this->cardUrl = $cardUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    /**
     * @param string|null $iconUrl
     * @return God
     */
    public function setIconUrl(?string $iconUrl): God
    {
        $this->iconUrl = $iconUrl;
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
     * @return God
     */
    public function setDateCreated(\DateTime $dateCreated): God
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
     * @return God
     */
    public function setDateUpdated(\DateTime $dateUpdated): God
    {
        $this->dateUpdated = $dateUpdated;
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
     * @return God
     */
    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;
        return $this;
    }
}
