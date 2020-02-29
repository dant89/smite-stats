<?php

namespace App\Controller;

use App\Entity\God;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GodController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SmiteService
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager , SmiteService $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
    }

    /**
     * @Route("/gods/", name="gods")
     * @return Response
     */
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findBy([], ['name' => 'ASC']);

        return $this->render('god/index.html.twig', [
            'gods' => $gods
        ]);
    }

    /**
     * @Route("/gods/pantheons/{pantheon}", name="gods_pantheon")
     * @param string $pantheon
     * @return Response
     */
    public function godsByPantheon(string $pantheon): Response
    {
        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findBy(['pantheon' => $pantheon], ['name' => 'ASC']);

        if (empty($gods)) {
            throw new NotFoundHttpException();
        }

        return $this->render('god/pantheon.html.twig', [
            'gods' => $gods,
            'pantheon' => strtolower($pantheon)
        ]);
    }

    /**
     * @Route("/gods/roles/{role}", name="gods_role")
     * @param string $role
     * @return Response
     */
    public function godsByRole(string $role): Response
    {
        $repository = $this->entityManager->getRepository(God::class);
        $gods = $repository->findBy(['roles' => $role], ['name' => 'ASC']);

        if (empty($gods)) {
            throw new NotFoundHttpException();
        }

        return $this->render('god/role.html.twig', [
            'gods' => $gods,
            'role' => strtolower($role)
        ]);
    }

    /**
     * @Route("/gods/{name}", name="god_view")
     * @param string $name
     * @return Response
     * @throws InvalidArgumentException
     */
    public function view(string $name): Response
    {
        $name = ucwords(str_replace('-', ' ', $name));
        $repository = $this->entityManager->getRepository(God::class);

        /** @var God $god */
        $god = $repository->findOneBy(['name' => $name]);
        if (is_null($god)) {
            throw new NotFoundHttpException();
        }

        $godDuelLeaderboard = $this->smite->getTopGods(440, $god->getSmiteId());
        $godJoustLeaderboard = $this->smite->getTopGods(450, $god->getSmiteId());
        $godConquestLeaderboard = $this->smite->getTopGods(451, $god->getSmiteId());

        if (is_array($godDuelLeaderboard)) {
            $godDuelLeaderboard = array_slice($godDuelLeaderboard, 0, 5, true);
        }
        if (is_array($godJoustLeaderboard)) {
            $godJoustLeaderboard = array_slice($godJoustLeaderboard,  0, 5, true);
        }
        if (is_array($godConquestLeaderboard)) {
            $godConquestLeaderboard = array_slice($godConquestLeaderboard,  0, 5, true);
        }

        $leaderboardTypes = [
            'Conquest' => $godConquestLeaderboard,
            'Duel' => $godDuelLeaderboard,
            'Joust' => $godJoustLeaderboard
        ];

        return $this->render('god/god.html.twig', [
            'god' => $god,
            'leaderboards' => $leaderboardTypes
        ]);
    }
}
