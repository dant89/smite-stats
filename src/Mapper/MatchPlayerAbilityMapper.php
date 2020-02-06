<?php

namespace App\Mapper;

use App\Entity\MatchItem;
use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerAbility;

class MatchPlayerAbilityMapper
{
    public function from(int $itemCount, MatchPlayer $matchPlayer, MatchItem $matchItem): MatchPlayerAbility
    {
        $matchPlayerAbility = new MatchPlayerAbility();
        $matchPlayerAbility->setAbility($matchItem);
        $matchPlayerAbility->setAbilityNumber($itemCount);
        $matchPlayerAbility->setMatchPlayer($matchPlayer);

        return $matchPlayerAbility;
    }
}
