<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_player_item", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_player_item", columns={"item_id", "item_name", "match_player_id"})})
 * @ORM\Entity
 */
class MatchPlayerItem
{
    /**
     * @ORM\ManyToOne(targetEntity="MatchPlayer", inversedBy="items")
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
     * @ORM\Column(name="item_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $itemId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="item_name", type="string", length=255, nullable=true)
     */
    private $itemName;

    /**
     * @var int
     *
     * @ORM\Column(name="item_number", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $itemNumber;

    /**
     * @return mixed
     */
    public function getMatchPlayer()
    {
        return $this->matchPlayer;
    }

    /**
     * @param mixed $matchPlayer
     * @return MatchPlayerItem
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
     * @return MatchPlayerItem
     */
    public function setId(int $id): MatchPlayerItem
    {
        $this->id = $id;
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
     * @return MatchPlayerItem
     */
    public function setItemId(?int $itemId): MatchPlayerItem
    {
        $this->itemId = $itemId;
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
     * @return MatchPlayerItem
     */
    public function setItemName(?string $itemName): MatchPlayerItem
    {
        $this->itemName = $itemName;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemNumber(): int
    {
        return $this->itemNumber;
    }

    /**
     * @param int $itemNumber
     * @return MatchPlayerItem
     */
    public function setItemNumber(int $itemNumber): MatchPlayerItem
    {
        $this->itemNumber = $itemNumber;
        return $this;
    }
}
