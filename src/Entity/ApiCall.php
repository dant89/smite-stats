<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * God
 *
 * @ORM\Table(name="api_call")
 * @ORM\Entity
 */
class ApiCall
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="response_status", type="integer", length=10, nullable=true)
     */
    private $responseStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="cached", type="integer", length=1, nullable=false)
     */
    private $cached = 0;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $dateCreated = '0000-00-00 00:00:00';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ApiCall
     */
    public function setId(int $id): ApiCall
    {
        $this->id = $id;
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
     * @return ApiCall
     */
    public function setName(string $name): ApiCall
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getResponseStatus(): ?int
    {
        return $this->responseStatus;
    }

    /**
     * @param int|null $responseStatus
     * @return ApiCall
     */
    public function setResponseStatus(?int $responseStatus): ApiCall
    {
        $this->responseStatus = $responseStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getCached(): int
    {
        return $this->cached;
    }

    /**
     * @param int $cached
     * @return ApiCall
     */
    public function setCached(int $cached): ApiCall
    {
        $this->cached = $cached;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime|null $dateCreated
     * @return ApiCall
     */
    public function setDateCreated(?\DateTime $dateCreated): ApiCall
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }
}
