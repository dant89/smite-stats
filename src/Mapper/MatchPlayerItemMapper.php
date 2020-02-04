<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerItem;

class MatchPlayerItemMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerItem
    {
        if (is_null($data["ActiveId{$banCount}"]) && is_null($data["Item_Active_{$banCount}"])) {
            return null;
        }

        $matchPlayerItem = new MatchPlayerItem();
        $matchPlayerItem->setItemId($data["ActiveId{$banCount}"] ?: null);
        $matchPlayerItem->setItemName($data["Item_Active_{$banCount}"] ?: null);
        $matchPlayerItem->setItemNumber($banCount);
        $matchPlayerItem->setMatchPlayer($matchPlayer);

        return $matchPlayerItem;
    }
}
