<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function getCount()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p.smitePlayerId)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findNewestPlayerNameNotNullQuery(int $limit = 0)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->where('p.name IS NOT NULL')
            ->orderBy('p.dateCreated', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();
    }

    public function findByTeamIdNotNull()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.teamId IS NOT NULL');

        $query = $qb->getQuery();
        return $query->execute();
    }
}
