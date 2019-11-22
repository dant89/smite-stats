<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ClanRemoveExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('clan_remove', [$this, 'clanRemove']),
        ];
    }

    public function clanRemove($subject)
    {
        return trim(preg_replace('/\[.*?\]/is', '', $subject));
    }
}
