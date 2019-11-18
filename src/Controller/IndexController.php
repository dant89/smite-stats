<?php

namespace App\Controller;

use App\Service\Smite;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(Smite $smite)
    {
        $this->smite = $smite;
    }

    /**
     * @Route("/", name="homepage")
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(): Response
    {
        $gods = $this->smite->getGodsFormatted();

        $gods = array_slice($gods, 1, 12, true);

        return $this->render('index/index.html.twig', [
            'gods' => $gods
        ]);
    }
}
