<?php

namespace App\Repository;

use App\Entity\MatchItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class MatchItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchItem::class);
    }

    public function getMatchItemsWithEffects()
    {
        $qb = $this->createQueryBuilder('mi');
        $qb->select('mi, me')
            ->leftJoin('mi.matchItemEffects', 'me')
            ->where('mi.active = 1')
            ->andWhere("mi.type = 'item'")
            ->orderBy('mi.itemName', 'ASC');
        $query = $qb->getQuery();
        return $query->execute();
    }
}
