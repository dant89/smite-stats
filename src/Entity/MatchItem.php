<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_item", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_item", columns={"item_id"})})
 * @ORM\Entity
 */
class MatchItem
{
    /**
     * @ORM\OneToMany(targetEntity="MatchPlayerItem", mappedBy="item")
     */
    private $matchPlayerItems;

    /**
     * @var int
     *
     * @ORM\Column(name="item_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $itemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="child_item_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $childItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="root_item_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $rootItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="active", type="integer", nullable=false, options={"unsigned"=true, "default"=0})
     */
    private $active = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="item_name", type="string", length=255, nullable=true)
     */
    private $itemName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="icon_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $iconId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $price;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tier", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $tier;

    /**
     * @var int|null
     *
     * @ORM\Column(name="starting_item", type="integer", nullable=true, options={"unsigned"=true, "default"=0})
     */
    private $startingItem = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="icon_url", type="string", length=255, nullable=true)
     */
    private $iconUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="secondary_description", type="text", nullable=true)
     */
    private $secondaryDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="restricted_roles", type="string", length=255, nullable=true)
     */
    private $restrictedRoles;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @return mixed
     */
    public function getMatchPlayerItems()
    {
        return $this->matchPlayerItems;
    }

    /**
     * @param mixed $matchPlayerItems
     * @return MatchItem
     */
    public function setMatchPlayerItems($matchPlayerItems)
    {
        $this->matchPlayerItems = $matchPlayerItems;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    /**
     * @param int|null $itemId
     * @return MatchItem
     */
    public function setItemId(?int $itemId): MatchItem
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getChildItemId(): ?int
    {
        return $this->childItemId;
    }

    /**
     * @param int|null $childItemId
     * @return MatchItem
     */
    public function setChildItemId(?int $childItemId): MatchItem
    {
        $this->childItemId = $childItemId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRootItemId(): ?int
    {
        return $this->rootItemId;
    }

    /**
     * @param int|null $rootItemId
     * @return MatchItem
     */
    public function setRootItemId(?int $rootItemId): MatchItem
    {
        $this->rootItemId = $rootItemId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getActive(): ?int
    {
        return $this->active;
    }

    /**
     * @param int|null $active
     * @return MatchItem
     */
    public function setActive(?int $active): MatchItem
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    /**
     * @param string|null $itemName
     * @return MatchItem
     */
    public function setItemName(?string $itemName): MatchItem
    {
        $this->itemName = $itemName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIconId(): ?int
    {
        return $this->iconId;
    }

    /**
     * @param int|null $iconId
     * @return MatchItem
     */
    public function setIconId(?int $iconId): MatchItem
    {
        $this->iconId = $iconId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     * @return MatchItem
     */
    public function setPrice(?int $price): MatchItem
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTier(): ?int
    {
        return $this->tier;
    }

    /**
     * @param int|null $tier
     * @return MatchItem
     */
    public function setTier(?int $tier): MatchItem
    {
        $this->tier = $tier;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStartingItem(): ?int
    {
        return $this->startingItem;
    }

    /**
     * @param int|null $startingItem
     * @return MatchItem
     */
    public function setStartingItem(?int $startingItem): MatchItem
    {
        $this->startingItem = $startingItem;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string|null $shortDescription
     * @return MatchItem
     */
    public function setShortDescription(?string $shortDescription): MatchItem
    {
        $this->shortDescription = $shortDescription;
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
     * @return MatchItem
     */
    public function setIconUrl(?string $iconUrl): MatchItem
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return MatchItem
     */
    public function setDescription(?string $description): MatchItem
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecondaryDescription(): ?string
    {
        return $this->secondaryDescription;
    }

    /**
     * @param string|null $secondaryDescription
     * @return MatchItem
     */
    public function setSecondaryDescription(?string $secondaryDescription): MatchItem
    {
        $this->secondaryDescription = $secondaryDescription;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRestrictedRoles(): ?string
    {
        return $this->restrictedRoles;
    }

    /**
     * @param string|null $restrictedRoles
     * @return MatchItem
     */
    public function setRestrictedRoles(?string $restrictedRoles): MatchItem
    {
        $this->restrictedRoles = $restrictedRoles;
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
     * @return MatchItem
     */
    public function setType(?string $type): MatchItem
    {
        $this->type = $type;
        return $this;
    }
}
