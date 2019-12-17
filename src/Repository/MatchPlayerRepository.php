<?php

namespace App\Repository;

use App\Entity\MatchPlayer;
use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class MatchPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchPlayer::class);
    }

    public function getUniqueMatchIds(Player $player)
    {
        $qb = $this->createQueryBuilder('m');
        $query = $qb->select('m.smiteMatchId')
            ->where('m.smitePlayer = :player')
            ->groupBy('m.smiteMatchId')
            ->setParameter('player', $player)
            ->getQuery();

        return $query->getScalarResult();
    }
}
