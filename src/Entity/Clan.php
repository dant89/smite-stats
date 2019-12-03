<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clan
 *
 * @ORM\Table(name="clan", uniqueConstraints={@ORM\UniqueConstraint(name="unique_clan_id", columns={"smite_clan_id"})})
 * @ORM\Entity
 */
class Clan
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
     * @ORM\Column(name="smite_clan_id", type="string", length=255, nullable=false)
     */
    private $smiteClanId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="founder", type="string", length=255, nullable=true)
     */
    private $founder;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wins", type="integer", nullable=false, options={"default":0})
     */
    private $wins = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="losses", type="integer", nullable=false, options={"default":0})
     */
    private $losses = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="players", type="integer", nullable=false, options={"default":0})
     */
    private $players = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rating", type="integer", nullable=false, options={"default":0})
     */
    private $rating = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tag", type="string", length=10, nullable=true)
     */
    private $tag;

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
     * @var \DateTime
     *
     * @ORM\Column(name="date_last_player_crawl", type="datetime", nullable=true)
     */
    private $dateLastPlayerCrawl;

    /**
     * @var int|null
     *
     * @ORM\Column(name="crawled", type="integer", nullable=false, options={"default":0})
     */
    private $crawled = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Clan
     */
    public function setId(int $id): Clan
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSmiteClanId(): string
    {
        return $this->smiteClanId;
    }

    /**
     * @param string $smiteClanId
     * @return Clan
     */
    public function setSmiteClanId(string $smiteClanId): Clan
    {
        $this->smiteClanId = $smiteClanId;
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
     * @return Clan
     */
    public function setName(?string $name): Clan
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFounder(): ?string
    {
        return $this->founder;
    }

    /**
     * @param string|null $founder
     * @return Clan
     */
    public function setFounder(?string $founder): Clan
    {
        $this->founder = $founder;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWins(): ?int
    {
        return $this->wins;
    }

    /**
     * @param int|null $wins
     * @return Clan
     */
    public function setWins(?int $wins): Clan
    {
        $this->wins = $wins;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLosses(): ?int
    {
        return $this->losses;
    }

    /**
     * @param int|null $losses
     * @return Clan
     */
    public function setLosses(?int $losses): Clan
    {
        $this->losses = $losses;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlayers(): ?int
    {
        return $this->players;
    }

    /**
     * @param int|null $players
     * @return Clan
     */
    public function setPlayers(?int $players): Clan
    {
        $this->players = $players;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int|null $rating
     * @return Clan
     */
    public function setRating(?int $rating): Clan
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag
     * @return Clan
     */
    public function setTag(?string $tag): Clan
    {
        $this->tag = $tag;
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
     * @return Clan
     */
    public function setDateCreated(\DateTime $dateCreated): Clan
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
     * @return Clan
     */
    public function setDateUpdated(\DateTime $dateUpdated): Clan
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastPlayerCrawl(): \DateTime
    {
        return $this->dateLastPlayerCrawl;
    }

    /**
     * @param \DateTime $dateLastPlayerCrawl
     * @return Clan
     */
    public function setDateLastPlayerCrawl(\DateTime $dateLastPlayerCrawl): Clan
    {
        $this->dateLastPlayerCrawl = $dateLastPlayerCrawl;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCrawled(): ?int
    {
        return $this->crawled;
    }

    /**
     * @param int|null $crawled
     * @return Clan
     */
    public function setCrawled(?int $crawled): Clan
    {
        $this->crawled = $crawled;
        return $this;
    }
}
