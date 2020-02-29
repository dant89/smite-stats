<?php

namespace App\Repository;

use App\Entity\God;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;

class GodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, God::class);
    }

    public function findTopKillGods(int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT g.`name`, mp.kills_player
            FROM (
                SELECT god_id, SUM(kills_player) AS kills_player
                FROM match_player 
                GROUP BY god_id
                ORDER BY kills_player DESC
                LIMIT :offset, :limit
            ) AS mp
            INNER JOIN god g ON mp.god_id = g.smite_id');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTopKdGods(int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT g.`name`, mp.kd
            FROM (
                SELECT god_id, (SUM(kills_player) / SUM(deaths)) AS kd
                FROM match_player 
                WHERE kills_player > 0 AND deaths > 0
                GROUP BY god_id
                ORDER BY kd DESC
                LIMIT :offset, :limit
            ) AS mp
            INNER JOIN god g ON mp.god_id = g.smite_id ');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTopKdaGods(int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT g.`name`, mp.kda
            FROM (
                SELECT god_id, (SUM(kills_player) + SUM(assists)) / SUM(deaths) AS kda
                FROM match_player 
                WHERE kills_player > 0 AND deaths > 0
                GROUP BY god_id
                ORDER BY kda DESC
                LIMIT :offset, :limit
            ) AS mp
            INNER JOIN god g ON mp.god_id = g.smite_id ');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
