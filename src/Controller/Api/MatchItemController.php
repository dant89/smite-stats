<?php

namespace App\Controller\Api;

use App\Entity\MatchItem;
use App\Entity\MatchItemEffect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MatchItemController
{
    /** @var AdapterInterface */
    protected $cache;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(
        AdapterInterface $cache,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->cache = $cache;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/match-items", name="api_match_items")
     */
    public function matchItems(): JsonResponse
    {
        $cache = $this->cache->getItem('api_match_items');
        if ($cache->isHit()) {
            return new JsonResponse($cache->get());
        }

        $matchItemsRepo = $this->entityManager->getRepository(MatchItem::class);
        $matchItems = $matchItemsRepo->getMatchItemsWithEffects();

        $data = [];
        /** @var MatchItem $matchItem */
        foreach ($matchItems as $matchItem) {

            $matchItemEffectsFormatted = [];
            /** @var MatchItemEffect $matchItemEffect */
            foreach ($matchItem->getMatchItemEffects() as $matchItemEffect) {
                $matchItemEffectsFormatted[] = [
                    'description' => $matchItemEffect->getDescription(),
                    'value' => (int) $matchItemEffect->getValue()
                ];
            }

            $data[] = [
                'id' => $matchItem->getItemId(),
                'name' => $matchItem->getItemName(),
                'child_item_id' => $matchItem->getChildItemId(),
                'root_item_id' => $matchItem->getRootItemId(),
                'price' => $matchItem->getPrice(),
                'tier' => $matchItem->getTier(),
                'starting_item' => $matchItem->getStartingItem(),
                'short_description' => $matchItem->getShortDescription(),
                'icon_url' => $matchItem->getIconUrl(),
                'description' => $matchItem->getDescription(),
                'restricted_roles' => $matchItem->getRestrictedRoles(),
                'type' => $matchItem->getType(),
                'secondary_description' => $matchItem->getSecondaryDescription(),
                'items' => $matchItemEffectsFormatted
            ];
        }

        $cache->set($data);
        $cache->expiresAfter(3600 * 6); // 6 hours
        $this->cache->save($cache);

        return new JsonResponse($data);
    }
}
