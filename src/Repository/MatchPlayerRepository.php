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
    
    public function getLatestClanMatchIds(int $clanId, int $limit = 10, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('mp');
        $qb->select('mp.smiteMatchId, COUNT(mp.smiteMatchId) AS total')
            ->where('mp.teamId = :teamId')
            ->groupBy('mp.smiteMatchId')
            ->orderBy('mp.smiteMatchId', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('teamId', $clanId);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function getLatestMatchIds(int $limit = 10, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('mp');
        $qb->select('mp.smiteMatchId')
            ->groupBy('mp.smiteMatchId')
            ->orderBy('mp.smiteMatchId', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function getMatchPlayersByIds(array $matchIds, int $limit = 10, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('mp');
        $qb->where('mp.smiteMatchId IN (:matchIds)')
            ->orderBy('mp.smiteMatchId', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('matchIds', $matchIds);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function getLatestMatches(int $limit = 10, int $offset = 0)
    {
        $qb = $this->createQueryBuilder('mp');
        $qb->groupBy('p.smiteMatchId')
            ->orderBy('p.smiteMatchId', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function getUniqueMatchIds(Player $player)
    {
        $qb = $this->createQueryBuilder('m');
        $query = $qb->select('m.smiteMatchId')
            ->where('m.smitePlayer = :player')
            ->groupBy('m.smiteMatchId')
            ->setParameter('player', $player)
            ->getQuery();

        return $query->getResult();
    }
}
