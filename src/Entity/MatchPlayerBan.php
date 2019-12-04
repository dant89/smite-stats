<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="match_player_ban", uniqueConstraints={@ORM\UniqueConstraint(name="unique_match_player_ban", columns={"ban_id", "ban_number", "match_player_id"})})
 * @ORM\Entity
 */
class MatchPlayerBan
{
    /**
     * @ORM\ManyToOne(targetEntity="MatchPlayer", inversedBy="bans")
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
     * @ORM\Column(name="ban_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $banId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ban_name", type="string", length=255, nullable=true)
     */
    private $banName;

    /**
     * @var int
     *
     * @ORM\Column(name="ban_number", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $banNumber;

    /**
     * @return mixed
     */
    public function getMatchPlayer()
    {
        return $this->matchPlayer;
    }

    /**
     * @param mixed $matchPlayer
     * @return MatchPlayerBan
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
     * @return MatchPlayerBan
     */
    public function setId(int $id): MatchPlayerBan
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBanId(): ?int
    {
        return $this->banId;
    }

    /**
     * @param int|null $banId
     * @return MatchPlayerBan
     */
    public function setBanId(?int $banId): MatchPlayerBan
    {
        $this->banId = $banId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBanName(): ?string
    {
        return $this->banName;
    }

    /**
     * @param string|null $banName
     * @return MatchPlayerBan
     */
    public function setBanName(?string $banName): MatchPlayerBan
    {
        $this->banName = $banName;
        return $this;
    }

    /**
     * @return int
     */
    public function getBanNumber(): int
    {
        return $this->banNumber;
    }

    /**
     * @param int $banNumber
     * @return MatchPlayerBan
     */
    public function setBanNumber(int $banNumber): MatchPlayerBan
    {
        $this->banNumber = $banNumber;
        return $this;
    }
}
