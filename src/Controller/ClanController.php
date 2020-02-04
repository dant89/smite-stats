<?php

namespace App\Controller;

use App\Entity\Clan;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class ClanController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SmiteService
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, SmiteService $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
    }

    /**
     * @Route("/clan/{name}", name="clan_view")
     * @param string $name
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(string $name): Response
    {
        // Check to see if we have this clan stored in the database
        $clanRepo = $this->entityManager->getRepository(Clan::class);
        /** @var Clan $clan */
        $clan = $clanRepo->findOneBy(['name' => $name]);

        // See if we can find this clan via the smite api
        if (is_null($clan) || ($clan->getCrawled() === 0)) {
            $clanResult = $this->smite->searchTeams($name);
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

            $clanDetails = $this->smite->getTeamDetails($clanId);
            if (!empty($clanDetails) && $clanDetails['TeamId'] !== 0) {
                $clan->setWins($clanDetails['Wins']);
                $clan->setLosses($clanDetails['Losses']);
                $clan->setRating($clanDetails['Rating']);
                $clan->setCrawled(1);
                $this->entityManager->persist($clan);
                $this->entityManager->flush();
            }
        }

        $gods = $this->smite->getGodsByNameKey();
        $teamPlayers = $this->smite->getTeamPlayers($clan->getSmiteClanId());

        foreach ($teamPlayers as &$teamPlayer) {
            if (!empty($teamPlayer['Name'])) {

                $playerDataByName = $this->smite->getPlayerIdByName($teamPlayer['Name']);
                $playerId = $playerDataByName['player_id'] ?? null;

                // TODO does this need get player by name AND details?
                if (!is_null($playerId)) {
                    $teamPlayer['Player_info'] = $this->smite->getPlayerDetailsByPortalId($playerId);

                    $teamPlayerId = $teamPlayer['Player_info']['Id'];

                    if (!is_null($teamPlayerId)) {
                        $playerGods = $this->smite->getPlayerGodDetails($teamPlayer['Player_info']['Id']);

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

        return $this->render('clan/clan.html.twig', [
            'clan' => $clan,
            'clan_players' => $teamPlayers,
            'gods' => $gods,
        ]);
    }
}
