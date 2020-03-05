<?php

namespace App\Service;

use App\Entity\MatchItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class MatchItemService
{
    /** @var AdapterInterface */
    protected $cache;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(AdapterInterface $cache, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    public function getActiveMatchItemItems(bool $cached = true)
    {
        if ($cached) {
            $cache = $this->cache->getItem('match_items_item');
            if ($cache->isHit()) {
                return $cache->get();
            }
        }

        $matchItemRepo = $this->entityManager->getRepository(MatchItem::class);
        $matchItems = $matchItemRepo->findBy(['type' => 'item', 'active' => 1], ['itemName' => 'ASC']);

        if ($cached) {
            $cache->set($matchItems);
            $cache->expiresAfter(3600 * 6); // 6 hours
            $this->cache->save($cache);
        }

        return $matchItems;
    }
}
