<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="god_skin", uniqueConstraints={@ORM\UniqueConstraint(name="unique_god_skin", columns={"id1", "id2"})})
 * @ORM\Entity
 */
class GodSkin
{
    /**
     * @ORM\ManyToOne(targetEntity="God", inversedBy="skins")
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
     * @ORM\Column(name="id1", type="integer", nullable=false)
     */
    private $idOne;

    /**
     * @var int
     *
     * @ORM\Column(name="id2", type="integer", nullable=false)
     */
    private $idTwo;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skin_url", type="string", length=255, nullable=true)
     */
    private $skinUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="obtainability", type="string", length=255, nullable=false)
     */
    private $obtainability;

    /**
     * @var int
     *
     * @ORM\Column(name="price_favor", type="integer", nullable=false, options={"default":0})
     */
    private $priceFavor;

    /**
     * @var int
     *
     * @ORM\Column(name="price_gems", type="integer", nullable=false, options={"default":0})
     */
    private $priceGems;

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
     * @return mixed
     */
    public function getGod()
    {
        return $this->god;
    }

    /**
     * @param mixed $god
     * @return GodSkin
     */
    public function setGod($god)
    {
        $this->god = $god;
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
     * @return GodSkin
     */
    public function setId(int $id): GodSkin
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdOne(): int
    {
        return $this->idOne;
    }

    /**
     * @param int $idOne
     * @return GodSkin
     */
    public function setIdOne(int $idOne): GodSkin
    {
        $this->idOne = $idOne;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdTwo(): int
    {
        return $this->idTwo;
    }

    /**
     * @param int $idTwo
     * @return GodSkin
     */
    public function setIdTwo(int $idTwo): GodSkin
    {
        $this->idTwo = $idTwo;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GodSkin
     */
    public function setName(string $name): GodSkin
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSkinUrl(): ?string
    {
        return $this->skinUrl;
    }

    /**
     * @param string|null $skinUrl
     * @return GodSkin
     */
    public function setSkinUrl(?string $skinUrl): GodSkin
    {
        $this->skinUrl = $skinUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getObtainability(): string
    {
        return $this->obtainability;
    }

    /**
     * @param string $obtainability
     * @return GodSkin
     */
    public function setObtainability(string $obtainability): GodSkin
    {
        $this->obtainability = $obtainability;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriceFavor(): int
    {
        return $this->priceFavor;
    }

    /**
     * @param int $priceFavor
     * @return GodSkin
     */
    public function setPriceFavor(int $priceFavor): GodSkin
    {
        $this->priceFavor = $priceFavor;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriceGems(): int
    {
        return $this->priceGems;
    }

    /**
     * @param int $priceGems
     * @return GodSkin
     */
    public function setPriceGems(int $priceGems): GodSkin
    {
        $this->priceGems = $priceGems;
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
     * @return GodSkin
     */
    public function setDateCreated(\DateTime $dateCreated): GodSkin
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
     * @return GodSkin
     */
    public function setDateUpdated(\DateTime $dateUpdated): GodSkin
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }
}
