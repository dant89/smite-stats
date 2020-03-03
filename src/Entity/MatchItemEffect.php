<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_item_effect", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_item_effect", columns={"match_item_id", "description", "value"})})
 * @ORM\Entity
 */
class MatchItemEffect
{
    /**
     * @ORM\ManyToOne(targetEntity="MatchItem", inversedBy="effects")
     * @ORM\JoinColumn(name="match_item_id", referencedColumnName="item_id")
     */
    private $matchItem;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=false, length=50)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", nullable=false, length=20)
     */
    private $value;

    /**
     * @return mixed
     */
    public function getMatchItem()
    {
        return $this->matchItem;
    }

    /**
     * @param mixed $matchItem
     * @return MatchItemEffect
     */
    public function setMatchItem($matchItem)
    {
        $this->matchItem = $matchItem;
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
     * @return MatchItemEffect
     */
    public function setId(int $id): MatchItemEffect
    {
        $this->id = $id;
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
     * @return MatchItemEffect
     */
    public function setDescription(string $description): MatchItemEffect
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return MatchItemEffect
     */
    public function setValue(string $value): MatchItemEffect
    {
        $this->value = $value;
        return $this;
    }
}
