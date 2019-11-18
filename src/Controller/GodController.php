<?php

namespace App\Controller;

use App\Service\Smite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class GodController extends AbstractController
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
     * @Route("/god/{name}", name="god_view")
     * @param string $name
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(string $name): Response
    {
        $name = ucwords(str_replace('-', ' ', $name));

        $gods = $this->smite->getGodsFormatted();
        if (!array_key_exists($name, $gods)) {
            throw new NotFoundHttpException();
        }

        $god = $gods[$name];

        return $this->render('god/god.html.twig', [
            'god' => $god,
        ]);
    }
}
