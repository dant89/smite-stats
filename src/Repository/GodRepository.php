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

        $stmt = $conn->prepare('SELECT g.name, mp.kills_player
            FROM god g
            INNER JOIN match_player mp ON g.smite_id = mp.god_id
            ORDER BY mp.kills_player DESC
            LIMIT :offset, :limit');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTopKdGods(int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT g.name, (mp.kills_player / mp.deaths) AS kd
            FROM god g
            INNER JOIN match_player mp ON g.smite_id = mp.god_id
            WHERE mp.kills_player > 0 AND mp.deaths > 0
            ORDER BY kd DESC
            LIMIT :offset, :limit');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTopKdaGods(int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT g.name, ((mp.kills_player + mp.assists) / mp.deaths) AS kda
            FROM god g
            INNER JOIN match_player mp ON g.smite_id = mp.god_id
            WHERE mp.kills_player > 0 AND mp.deaths > 0
            ORDER BY kda DESC
            LIMIT :offset, :limit');

        $stmt->bindParam(':limit', $limit, ParameterType::INTEGER);
        $stmt->bindParam(':offset', $offset, ParameterType::INTEGER);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
