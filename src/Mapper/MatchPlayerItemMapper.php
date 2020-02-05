<?php

namespace App\Mapper;

use App\Entity\MatchItem;
use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerItem;

class MatchPlayerItemMapper
{
    public function from(int $itemCount, MatchPlayer $matchPlayer, MatchItem $matchItem): MatchPlayerItem
    {
        $matchPlayerItem = new MatchPlayerItem();
        $matchPlayerItem->setItem($matchItem);
        $matchPlayerItem->setItemNumber($itemCount);
        $matchPlayerItem->setMatchPlayer($matchPlayer);

        return $matchPlayerItem;
    }
}
