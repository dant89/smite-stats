<?php

namespace App\Mapper;

use App\Entity\MappedMatch;
use App\Entity\MatchPlayer;

class MatchMapper
{
    /**
     * We need to accept multiple matchPlayers and format them into an array of unique Matches
     *
     * @param MatchPlayer[] $matchPlayers
     * @return array
     */
    public function to($matchPlayers = []): array
    {
        $matches = [];
        $formattedMatches = [];

        /** @var MatchPlayer $matchPlayer */
        foreach ($matchPlayers as $matchPlayer) {
            $formattedMatches[$matchPlayer->getSmiteMatchId()][] = $matchPlayer;
        }

        foreach ($formattedMatches as $formattedMatch) {
            $firstRawMatch = $formattedMatch[0];

            $match = new MappedMatch();
            $match->setSmiteMatchId($firstRawMatch->getSmiteMatchId());
            $match->setMinutes($firstRawMatch->getMinutes());
            $match->setDate($firstRawMatch->getEntryDatetime());
            $match->setMatchType($firstRawMatch->getMapGame());
            $match->setWinningTaskforce($firstRawMatch->getWinningTaskForce());
            $match->setMatchPlayers($formattedMatch);
            $matches[$match->getSmiteMatchId()] = $match;
        }

        return $matches;
    }
}
