<?php

namespace App\Service;

use App\Entity\MatchPlayer;
use App\Mapper\MatchMapper;
use App\Repository\MatchPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClanService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /** @var MatchMapper */
    protected $matchMapper;

    public function __construct(EntityManagerInterface $entityManager, MatchMapper $matchMapper)
    {
        $this->entityManager = $entityManager;
        $this->matchMapper = $matchMapper;
    }

    public function getLatestClanMatches(int $clanId, int $limit = 10, int $offset = 0)
    {
        /** @var MatchPlayerRepository $matchPlayerRepo */
        $matchPlayerRepo = $this->entityManager->getRepository(MatchPlayer::class);
        $latestMatchIdsArray = $matchPlayerRepo->getLatestClanMatchIds($clanId, $limit, $offset);

        $matchIds = [];
        foreach ($latestMatchIdsArray as $latestMatchId) {
            if ($latestMatchId['total'] > 1) {
                $matchIds[] = $latestMatchId['smiteMatchId'];
            }
        }

        $matchPlayers = $matchPlayerRepo->getMatchPlayersByIds($matchIds, 10, 0);
        $matches = $this->matchMapper->to($matchPlayers);

        return $matches;
    }
}
