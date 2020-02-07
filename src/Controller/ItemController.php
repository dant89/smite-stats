<?php

namespace App\Controller;

use App\Entity\MatchItem;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
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
     * @Route("/items/", name="items")
     * @return Response
     */
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(MatchItem::class);

        $relics = $repository->findBy(['type' => 'active', 'active' => 1], ['itemName' => 'ASC']);
        $consumables = $repository->findBy(['type' => 'consumable', 'active' => 1], ['itemName' => 'ASC']);
        $items = $repository->findBy(['type' => 'item', 'active' => 1], ['itemName' => 'ASC']);

        return $this->render('item/index.html.twig', [
            'relics' => $relics,
            'consumables' => $consumables,
            'items' => $items,
        ]);
    }

    /**
     * @Route("/consumables/{name}", name="consumable_view")
     * @param string $name
     * @return Response
     */
    public function consumableView(string $name): Response
    {
        $name = ucwords(str_replace('-', ' ', $name));
        $repository = $this->entityManager->getRepository(MatchItem::class);

        /** @var MatchItem $item */
        $item = $repository->findOneBy([
            'itemName' => $name,
            'type' => 'consumable',
            'active' => 1
        ]);
        if (is_null($item)) {
            throw new NotFoundHttpException();
        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'type_singular' => 'consumable',
            'type_uri' => 'consumables',
            'type' => 'Consumable'
        ]);
    }

    /**
     * @Route("/items/{name}", name="item_view")
     * @param string $name
     * @return Response
     */
    public function itemView(string $name): Response
    {
        $name = ucwords(str_replace('-', ' ', $name));
        $repository = $this->entityManager->getRepository(MatchItem::class);

        /** @var MatchItem $item */
        $item = $repository->findOneBy([
            'itemName' => $name,
            'type' => 'item',
            'active' => 1
        ]);
        if (is_null($item)) {
            throw new NotFoundHttpException();
        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'type_singular' => 'item',
            'type_uri' => 'items',
            'type' => 'Item'
        ]);
    }

    /**
     * @Route("/relics/{name}", name="relic_view")
     * @param string $name
     * @return Response
     */
    public function relicView(string $name): Response
    {
        $name = ucwords(str_replace('-', ' ', $name));
        $repository = $this->entityManager->getRepository(MatchItem::class);

        /** @var MatchItem $item */
        $item = $repository->findOneBy([
            'itemName' => $name,
            'type' => 'active',
            'active' => 1
        ]);
        if (is_null($item)) {
            throw new NotFoundHttpException();
        }

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'type_singular' => 'relic',
            'type_uri' => 'relics',
            'type' => 'Relic'
        ]);
    }
}
