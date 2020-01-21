<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/player-sitemap.xml", name="sitemap_players")
     * @return Response
     */
    public function sitemapPlayerIndex(): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $players = $playerRepo->getCount();

        $pages = ceil($players / 10000);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render('sitemap/index.xml.twig', [
            'pages' => $pages
        ], $response);
    }

    /**
     * @Route("/player-sitemap-{page}.xml", name="sitemap_players_paged")
     * @param int $page
     * @return Response
     */
    public function players(int $page = 1): Response
    {
        $offset = 0;
        if ($page > 1) {
            $offset = ($page - 1) * 10000;
        }

        $playerRepo = $this->entityManager->getRepository(Player::class);
        $players = $playerRepo->findBy([], ['smitePlayerId' => 'asc'], 10000, $offset);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render('sitemap/players.xml.twig', [
            'players' => $players
        ], $response);
    }
}
