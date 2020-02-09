<?php

namespace App\Controller\Api;

use App\Entity\Player;
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

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /** @var SmiteService */
    protected $smite;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        SmiteService $smite
    ) {
        $this->entityManager = $entityManager;
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

        $player->setAvatarUrl($playerDetails['Avatar_URL']);
        $player->setDateRegistered(new \DateTime($playerDetails['Created_Datetime']));
        $player->setDateLastLogin(new \DateTime($playerDetails['Last_Login_Datetime']));
        $player->setHoursPlayed($playerDetails['HoursPlayed'] ?? 0);
        $player->setLeaves($playerDetails['Leaves'] ?? 0);
        $player->setLevel($playerDetails['Level'] ?? 0);
        $player->setLosses($playerDetails['Losses'] ?? 0);
        $player->setMasteryLevel($playerDetails['MasteryLevel'] ?? 0);
        $player->setName($playerDetails['Name']);
        $player->setPersonalStatusMessage($playerDetails['Personal_Status_Message']);
        $player->setRankStatConquest($playerDetails['Rank_Stat_Conquest'] ?? 0);
        $player->setRankStatDuel($playerDetails['Rank_Stat_Duel'] ?? 0);
        $player->setRankStatJoust($playerDetails['Rank_Stat_Joust'] ?? 0);
        $player->setRegion($playerDetails['Region']);
        $player->setTeamId($playerDetails['TeamId']);
        $player->setTeamName($playerDetails['Team_Name']);
        $player->setTierConquest($playerDetails['Tier_Conquest'] ?? 0);
        $player->setTierDuel($playerDetails['Tier_Duel'] ?? 0);
        $player->setTierJoust($playerDetails['Tier_Joust'] ?? 0);
        $player->setTotalAchievements($playerDetails['Total_Achievements'] ?? 0);
        $player->setTotalWorshippers($playerDetails['Total_Worshippers'] ?? 0);
        $player->setWins($playerDetails['Wins'] ?? 0);
        $player->setCrawled(1);
        $player->setDateUpdated(new \DateTime());
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
