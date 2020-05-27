<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;

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

    public function getCountPlayersPerMasteryLevel()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.smitePlayerId) AS total, p.masteryLevel')
            ->where('p.masteryLevel > 0')
            ->groupBy('p.masteryLevel');

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function getCountPlayersPerLevel()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.smitePlayerId) AS total, p.level')
            ->where('p.level > 0')
            ->groupBy('p.level');

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findPlayerIdsNameNotNullAsc(int $limit = 0, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.smitePlayerId, p.name')
            ->where('p.name IS NOT NULL')
            ->orderBy('p.smitePlayerId', 'ASC')
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

    public function findCountOfPlayersByLevel()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT `level`, SUM(`level`) AS `count`
            FROM player
            GROUP BY `level`
            ORDER BY SUM(`level`) DESC');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findCountOfPlayersByTier(string $type)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT `tier_' . $type . '`, SUM(`tier_' . $type . '`) AS `tier_sum`
            FROM player
            GROUP BY `tier_' . $type . '`
            ORDER BY `tier_' . $type . '` ASC');

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
