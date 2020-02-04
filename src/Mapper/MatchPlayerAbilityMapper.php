<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;

class MatchPlayerAbilityMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerAbility
    {
        $matchPlayerAbility = new MatchPlayerAbility();
        $matchPlayerAbility->setAbilityId($data["ItemId{$banCount}"] ?: null);
        $matchPlayerAbility->setAbilityName($data["Item_Purch_{$banCount}"] ?: null);
        $matchPlayerAbility->setAbilityNumber($banCount);
        $matchPlayerAbility->setMatchPlayer($matchPlayer);

        if (is_null($matchPlayerAbility->getAbilityId()) && is_null($matchPlayerAbility->getAbilityName())) {
            return null;
        }

        return $matchPlayerAbility;
    }
}
