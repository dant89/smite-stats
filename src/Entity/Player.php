<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity
 */
class Player
{
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
     * @ORM\Column(name="smite_player_id", type="string", length=255, nullable=false)
     */
    private $smitePlayerId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $dateCreated = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $dateUpdated = '0000-00-00 00:00:00';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Player
     */
    public function setId(int $id): Player
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSmitePlayerId(): string
    {
        return $this->smitePlayerId;
    }

    /**
     * @param string $smitePlayerId
     * @return Player
     */
    public function setSmitePlayerId(string $smitePlayerId): Player
    {
        $this->smitePlayerId = $smitePlayerId;
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
     * @return Player
     */
    public function setName(?string $name): Player
    {
        $this->name = $name;
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
     * @return Player
     */
    public function setDateCreated(\DateTime $dateCreated): Player
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
     * @return Player
     */
    public function setDateUpdated(\DateTime $dateUpdated): Player
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }
}
