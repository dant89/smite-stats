<?php

namespace App\Controller;

use App\Entity\MatchPlayer;
use App\Mapper\MatchMapper;
use App\Repository\MatchPlayerRepository;
use App\Service\PlayerService;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var MatchMapper */
    protected $matchMapper;

    /** @var PlayerService */
    protected $playerService;

    /** @var SmiteService */
    protected $smiteService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MatchMapper $matchMapper,
        PlayerService $playerService,
        SmiteService $smiteService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->matchMapper = $matchMapper;
        $this->playerService = $playerService;
        $this->smiteService = $smiteService;
    }

    /**
     * @Route("/matches/", name="matches")
     * @param int id
     * @return Response
     */
    public function index(): Response
    {
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);

        /** @var MatchPlayerRepository $matchPlayerRepo */
        $latestMatchIdsArray = $matchPlayerRepo->getLatestMatchIds(10);
        $latestMatchIds = array_column($latestMatchIdsArray, 'smiteMatchId');

        $matchPlayers = $matchPlayerRepo->getMatchPlayersByIds($latestMatchIds);
        $matches = $this->matchMapper->to($matchPlayers);

        return $this->render('matches/index.html.twig', [
            'matches' => $matches
        ]);
    }

    /**
     * @Route("/matches/{id}", name="match_view")
     * @param int id
     * @return Response
     */
    public function view(int $id): Response
    {
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);
        $matchPlayers = $matchPlayerRepo->findBy(['smiteMatchId' => $id]);
        if (empty($matchPlayers)) {
            throw new NotFoundHttpException();
        }

        $matches = $this->matchMapper->to($matchPlayers);
        $match = reset($matches);

        return $this->render('matches/match.html.twig', [
            'match' => $match
        ]);
    }
}

