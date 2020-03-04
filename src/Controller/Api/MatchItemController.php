<?php

namespace App\Controller\Api;

use App\Entity\MatchItem;
use App\Entity\MatchItemEffect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MatchItemController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/match-items", name="api_match_items")
     * @return JsonResponse
     */
    public function matchItems(): JsonResponse
    {
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

        return new JsonResponse($data);

        $jsonMatchItems = $this->serializer->serialize($matchItems, 'json', [
            'circular_reference_handler' => function () {
                return null;
            }
        ]);

        return new JsonResponse($jsonMatchItems, 200, [], true);
    }
}
