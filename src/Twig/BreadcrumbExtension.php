<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumb_schema', [$this, 'renderBreadcrumbSchema'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Generate structured data for breadcrumb navigation
     */
    public function renderBreadcrumbSchema(array $items): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $position = 1;
        foreach ($items as $item) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
            $position++;
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
