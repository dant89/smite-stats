<?php

namespace App\Mapper;

use App\Entity\MatchPlayer;
use App\Entity\MatchPlayerItem;

class MatchPlayerItemMapper
{
    public function from(array $data, int $banCount, MatchPlayer $matchPlayer): ?MatchPlayerItem
    {
        $matchPlayerItem = new MatchPlayerItem();
        $matchPlayerItem->setItemId($data["ActiveId{$banCount}"] ?: null);
        $matchPlayerItem->setItemName($data["Item_Active_{$banCount}"] ?: null);
        $matchPlayerItem->setItemNumber($banCount);
        $matchPlayerItem->setMatchPlayer($matchPlayer);

        if (is_null($matchPlayerItem->getItemId()) && is_null($matchPlayerItem->getItemName())) {
            return null;
        }

        return $matchPlayerItem;
    }
}
