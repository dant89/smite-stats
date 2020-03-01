<?php

namespace App\Controller;

use App\Entity\Clan;
use App\Service\ClanService;
use App\Service\GodService;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class ClanController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ClanService */
    protected $clanService;

    /** @var GodService */
    protected $godService;

    /** @var SmiteService */
    protected $smiteService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClanService $clanService,
        GodService $godService,
        SmiteService $smiteService
    ) {
        $this->entityManager = $entityManager;
        $this->clanService = $clanService;
        $this->godService = $godService;
        $this->smiteService = $smiteService;
    }

    /**
     * @Route("/clan/{name}", name="clan_view")
     * @param string $name
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(string $name): Response
    {
        $clanRepo = $this->entityManager->getRepository(Clan::class);

        /** @var Clan $clan */
        $clan = $clanRepo->findOneBy(['name' => $name]);

        // See if we can find this clan via the smite api
        if (is_null($clan) || ($clan->getCrawled() === 0)) {
            $clanResult = $this->smiteService->searchTeams($name);
            $clanId = $clanResult['TeamId'] ?? null;
            if (is_null($clanId)) {
                throw new NotFoundHttpException();
            }

            $clan = new Clan();
            $clan->setSmiteClanId($clanId);
            $clan->setName($clanResult['Name']);
            $clan->setFounder($clanResult['Founder']);
            $clan->setTag($clanResult['Tag']);
            $clan->setPlayers($clanResult['Players']);
            $clan->setDateCreated(new \DateTime());
            $clan->setDateUpdated(new \DateTime());
            $this->entityManager->persist($clan);
            $this->entityManager->flush();

            $clanDetails = $this->smiteService->getTeamDetails($clanId);
            if (!empty($clanDetails) && $clanDetails['TeamId'] !== 0) {
                $clan->setWins($clanDetails['Wins']);
                $clan->setLosses($clanDetails['Losses']);
                $clan->setRating($clanDetails['Rating']);
                $clan->setCrawled(1);
                $this->entityManager->persist($clan);
                $this->entityManager->flush();
            }
        }

        $gods = $this->godService->getGodsByNameKey();
        $teamPlayers = $this->smiteService->getTeamPlayers($clan->getSmiteClanId());

        // TODO tidy this logic
        foreach ($teamPlayers as &$teamPlayer) {
            if (!empty($teamPlayer['Name'])) {

                $playerDataByName = $this->smiteService->getPlayerIdByName($teamPlayer['Name']);
                $playerId = $playerDataByName['player_id'] ?? null;

                // TODO does this need get player by name AND details?
                if (!is_null($playerId)) {
                    $teamPlayer['Player_info'] = $this->smiteService->getPlayerDetailsByPortalId($playerId);

                    $teamPlayerId = $teamPlayer['Player_info']['Id'];

                    if (!is_null($teamPlayerId)) {
                        $playerGods = $this->smiteService->getPlayerGodDetails($teamPlayer['Player_info']['Id']);

                        $playerStats = [
                            'Kills' => 0,
                            'Assists' => 0,
                            'Deaths' => 0,
                        ];

                        foreach ($playerGods as $playerGod) {
                            $playerStats['Kills'] += $playerGod['Kills'];
                            $playerStats['Assists'] += $playerGod['Assists'];
                            $playerStats['Deaths'] += $playerGod['Deaths'];
                        }

                        $teamPlayer['God_info'] = array_slice($playerGods, 0, 4, true);
                        $teamPlayer['Stats_info'] = $playerStats;
                    }
                }
            }
        }

        $matches = $this->clanService->getLatestClanMatches($clan->getSmiteClanId());

        return $this->render('clan/clan.html.twig', [
            'clan' => $clan,
            'clan_players' => $teamPlayers,
            'gods' => $gods,
            'matches' => $matches
        ]);
    }
}
