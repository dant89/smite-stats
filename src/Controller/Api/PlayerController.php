<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Mapper\PlayerMapper;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var PlayerMapper */
    protected $playerMapper;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var SmiteService */
    protected $smite;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerMapper $playerMapper,
        SerializerInterface $serializer,
        SmiteService $smite
    ) {
        $this->entityManager = $entityManager;
        $this->playerMapper = $playerMapper;
        $this->serializer = $serializer;
        $this->smite = $smite;
    }

    /**
     * @Route("/api/player/update/{id}", name="api_player_update")
     * @param string $id
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function updatePlayer(string $id): JsonResponse
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);

        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);
        if (is_null($player)) {
            throw new NotFoundHttpException();
        }

        $playerUpdated = $player->getDateUpdated()->diff(new \DateTime());
        $playerUpdatedMins = $playerUpdated->days * 24 * 60;
        $playerUpdatedMins += $playerUpdated->h * 60;
        $playerUpdatedMins += $playerUpdated->i;
        if ($playerUpdatedMins <= 15) {
            throw new AccessDeniedHttpException('Player updated recently, please wait longer.');
        }

        $playerDetails = $this->smite->getPlayerDetailsByPortalId($id);
        if (empty($playerDetails)) {
            throw new NotFoundHttpException();
        }

        $player = $this->playerMapper->fromExisting($player, $playerDetails);
        $this->entityManager->persist($player);
        $this->entityManager->flush();

        $jsonPlayer = $this->serializer->serialize($player, 'json', [
            'circular_reference_handler' => function () {
                return null;
            }
        ]);

        return new JsonResponse($jsonPlayer);
    }
}
