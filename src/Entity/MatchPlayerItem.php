<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_player_item", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_player_item", columns={"item_id", "item_number", "match_player_id"})})
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
     * @ORM\ManyToOne(targetEntity="MatchItem", inversedBy="matchPlayerItems")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="item_id")
     */
    private $item;

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
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     * @return MatchPlayerItem
     */
    public function setItem($item)
    {
        $this->item = $item;
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
