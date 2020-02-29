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

    public function getTopKillGods(int $limit = 10, int $offset = 0): array
    {
        $cache = $this->cache->getItem('gods_top_kills');
        if ($cache->isHit()) {
            return $cache->get();
        }

        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findTopKillGods($limit, $offset);

        $cache->set($gods);
        $cache->expiresAfter(3600 * 6); // 6 hours
        $this->cache->save($cache);

        return $gods;
    }

    public function getTopKdGods(int $limit = 10, int $offset = 0): array
    {
        $cache = $this->cache->getItem('gods_top_kd');
        if ($cache->isHit()) {
            return $cache->get();
        }

        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findTopKdGods($limit, $offset);

        $cache->set($gods);
        $cache->expiresAfter(3600 * 6); // 6 hours
        $this->cache->save($cache);

        return $gods;
    }

    public function getTopKdaGods(int $limit = 10, int $offset = 0): array
    {
        $cache = $this->cache->getItem('gods_top_kda');
        if ($cache->isHit()) {
            return $cache->get();
        }

        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findTopKdaGods($limit, $offset);

        $cache->set($gods);
        $cache->expiresAfter(3600 * 6); // 6 hours
        $this->cache->save($cache);

        return $gods;
    }
}
