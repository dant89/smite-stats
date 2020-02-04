<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerBan;

class MatchPlayerBanMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerBan
    {
        $matchPlayerBan = new MatchPlayerBan();
        $matchPlayerBan->setBanId($data["Ban{$banCount}Id"] ?: null);
        $matchPlayerBan->setBanName($data["Ban{$banCount}"] ?: null);
        $matchPlayerBan->setBanNumber($banCount);
        $matchPlayerBan->setMatchPlayer($matchPlayer);

        if (is_null($matchPlayerBan->getBanId()) && is_null($matchPlayerBan->getBanName())) {
            return null;
        }

        return $matchPlayerBan;
    }
}
