<?php

namespace App\Service;

class HelperService
{
    public function getMinutesLastUpdated(\DateTime $lastUpdated): int
    {
        $lastUpdatedDiff = $lastUpdated->diff(new \DateTime());
        $updatedMinsAgo = $lastUpdatedDiff->days * 24 * 60;
        $updatedMinsAgo += $lastUpdatedDiff->h * 60;
        $updatedMinsAgo += $lastUpdatedDiff->i;

        return $updatedMinsAgo;
    }
}
