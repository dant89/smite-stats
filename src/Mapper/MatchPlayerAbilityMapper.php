<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;

class MatchPlayerAbilityMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerAbility
    {
        if (is_null($data["ItemId{$banCount}"]) && is_null($data["Item_Purch_{$banCount}"])) {
            return null;
        }

        $matchPlayerAbility = new MatchPlayerAbility();
        $matchPlayerAbility->setAbilityId($data["ItemId{$banCount}"] ?: null);
        $matchPlayerAbility->setAbilityName($data["Item_Purch_{$banCount}"] ?: null);
        $matchPlayerAbility->setAbilityNumber($banCount);
        $matchPlayerAbility->setMatchPlayer($matchPlayer);

        return $matchPlayerAbility;
    }
}
