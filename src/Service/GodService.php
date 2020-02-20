<?php

namespace App\Service;

use App\Entity\God;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Psr\Cache\InvalidArgumentException;

class GodService
{
    /** @var AdapterInterface */
    protected $cache;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(AdapterInterface $cache, EntityManagerInterface $entityManager)
    {
        $this->cache = $cache;
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $count
     * @param bool $cached
     * @return array
     * @throws InvalidArgumentException
     */
    public function getFeaturedGods(int $count, bool $cached = true): array
    {
        if ($cached) {
            $cache = $this->cache->getItem('index_featured_gods');
            if ($cache->isHit()) {
                return $cache->get();
            }
        }

        $gods = $this->getGodsByNameKey();
        shuffle($gods);
        $gods = array_slice($gods, 0, $count, true);

        if ($cached) {
            $cache->set($gods);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        }

        return $gods;
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
