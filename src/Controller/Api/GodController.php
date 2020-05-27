<?php

namespace App\Controller\Api;

use App\Entity\God;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GodController
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
     * @Route("/api/gods", name="api_gods")
     */
    public function gods(): JsonResponse
    {
        $cache = $this->cache->getItem('api_gods');
        if ($cache->isHit()) {
            return new JsonResponse($cache->get(), 200, [], true);
        }

        $godsRepo = $this->entityManager->getRepository(God::class);
        $gods = $godsRepo->findAll();

        $jsonGods = $this->serializer->serialize($gods, 'json', [
            'circular_reference_handler' => function () {
                return null;
            }
        ]);

        $cache->set($jsonGods);
        $cache->expiresAfter(3600 * 6); // 6 hours
        $this->cache->save($cache);

        return new JsonResponse($jsonGods, 200, [], true);
    }
}
