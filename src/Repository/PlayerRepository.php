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

    public function getCountNameNotNull()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.smitePlayerId)')
            ->where('p.name IS NOT NULL');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPlayerIdsNameNotNullAsc(int $limit = 0, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.smitePlayerId, p.name')
            ->where('p.name IS NOT NULL')
            ->orderBy('p.dateCreated', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $query = $qb->getQuery();
        return $query->execute();
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
