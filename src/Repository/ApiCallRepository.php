<?php

namespace App\Repository;

use App\Entity\ApiCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ApiCallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiCall::class);
    }

    public function getDailyApiUsageStats()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $stmt = $conn->prepare('SELECT COUNT(*) AS total_calls, DATE(date_created) AS call_date
            FROM api_call
            GROUP BY call_date
            ORDER BY call_date DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
