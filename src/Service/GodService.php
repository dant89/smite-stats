<?php

namespace App\Service;

use App\Entity\God;
use Doctrine\ORM\EntityManagerInterface;

class GodService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getGodsByNameKey(): array
    {
        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findAll();

        $formattedGods = [];

        /** @var God $god */
        foreach ($gods as $god) {
            $formattedGods[$god->getName()] = $god;
        }

        return $formattedGods;
    }
}
