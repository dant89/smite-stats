<?php

namespace App\Controller;

use App\Entity\MatchItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuildController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/builds/", name="builds")
     */
    public function index(): Response
    {
        return $this->render('build/index.html.twig', []);
    }

    /**
     * @Route("/builds/{id}", name="build_view", requirements={"id": "[-\d]+"})
     */
    public function build(int $id): Response
    {
        return $this->render('build/build.html.twig', []);
    }

    /**
     * @Route("/builds/create", name="build_create")
     */
    public function create(): Response
    {
        $matchItemRepo = $this->entityManager->getRepository(MatchItem::class);
        $matchItems = $matchItemRepo->findBy(['type' => 'item', 'active' => 1], ['itemName' => 'ASC']);

        return $this->render('build/create.html.twig', [
            'match_items' => $matchItems
        ]);
    }
}
