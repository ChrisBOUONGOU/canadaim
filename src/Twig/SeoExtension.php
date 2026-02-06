<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('og_tag', [$this, 'renderOgTag'], ['is_safe' => ['html']]),
            new TwigFunction('twitter_card', [$this, 'renderTwitterCard'], ['is_safe' => ['html']]),
        ];
    }

    public function renderOgTag(string $property, string $content): string
    {
        return sprintf('<meta property="og:%s" content="%s">', htmlspecialchars($property), htmlspecialchars($content));
    }

    public function renderTwitterCard(string $name, string $content): string
    {
        return sprintf('<meta name="twitter:%s" content="%s">', htmlspecialchars($name), htmlspecialchars($content));
    }
}
