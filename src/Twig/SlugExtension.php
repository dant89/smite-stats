<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SlugExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('slug', [$this, 'createSlug']),
        ];
    }

    public function createSlug($subject)
    {
        $noWhitespace = trim($subject);
        $hyphenated = preg_replace('/([^a-z0-9-]+)/is', '-', $noWhitespace);
        $trailingHyphensRemoved = trim($hyphenated, '-');
        $lower = strtolower($trailingHyphensRemoved);

        return $lower;
    }
}
