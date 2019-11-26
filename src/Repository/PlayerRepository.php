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

    public function findByTeamIdNotNull()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.teamId IS NOT NULL');

        $query = $qb->getQuery();
        return $query->execute();
    }
}
