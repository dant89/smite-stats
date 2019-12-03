<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="search_player")
 * @ORM\Entity
 */
class PlayerSearch
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
     * @ORM\Column(name="term", type="string", length=255, nullable=false)
     */
    private $term;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_searched", type="datetime", nullable=false)
     */
    private $dateSearched;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlayerSearch
     */
    public function setId(int $id): PlayerSearch
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @param string $term
     * @return PlayerSearch
     */
    public function setTerm(string $term): PlayerSearch
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateSearched(): \DateTime
    {
        return $this->dateSearched;
    }

    /**
     * @param \DateTime $dateSearched
     * @return PlayerSearch
     */
    public function setDateSearched(\DateTime $dateSearched): PlayerSearch
    {
        $this->dateSearched = $dateSearched;
        return $this;
    }
}
