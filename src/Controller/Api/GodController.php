<?php

namespace App\Controller\Api;

use App\Entity\God;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GodController
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
     * @Route("/api/gods", name="api_gods")
     * @return JsonResponse
     */
    public function gods(): JsonResponse
    {
        $godsRepo = $this->entityManager->getRepository(God::class);
        $gods = $godsRepo->findAll();

        $jsonGods = $this->serializer->serialize($gods, 'json', [
            'circular_reference_handler' => function () {
                return null;
            }
        ]);

        return new JsonResponse($jsonGods, 200, [], true);
    }
}
