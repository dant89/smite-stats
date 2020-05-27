<?php

namespace App\Controller;

use App\Service\MatchItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuildController extends AbstractController
{
    /** @var MatchItemService */
    private $matchItemService;

    public function __construct(MatchItemService $matchItemService)
    {
        $this->matchItemService = $matchItemService;
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
        $matchItems = $this->matchItemService->getActiveMatchItemItems();

        return $this->render('build/create.html.twig', [
            'match_items' => $matchItems
        ]);
    }
}
