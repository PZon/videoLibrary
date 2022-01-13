<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('functionName', [$this, 'doSomething']),
        ];
    }

    public function slugify($value)
    {
        $value = preg_replace("/ +/", "-", trim($value));
        $value = mb_strtolower(preg_replace('/[^A-Za-z0-9]/','',$value),'UTF-8');
        return $value;
    }
}
