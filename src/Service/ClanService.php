<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class ClanService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
