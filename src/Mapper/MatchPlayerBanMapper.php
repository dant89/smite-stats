<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerBan;

class MatchPlayerBanMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerBan
    {
        if (is_null($data["Ban{$banCount}Id"]) && is_null($data["Ban{$banCount}"])) {
            return null;
        }

        $matchPlayerBan = new MatchPlayerBan();
        $matchPlayerBan->setBanId($data["Ban{$banCount}Id"] ?: null);
        $matchPlayerBan->setBanName($data["Ban{$banCount}"] ?: null);
        $matchPlayerBan->setBanNumber($banCount);
        $matchPlayerBan->setMatchPlayer($matchPlayer);

        return $matchPlayerBan;
    }
}
