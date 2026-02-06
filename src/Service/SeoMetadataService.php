<?php

namespace App\Service;

/**
 * Service for managing SEO metadata across pages
 */
class SeoMetadataService
{
    private const METADATA = [
        'home' => [
            'title' => 'Canadaim - Immigration, Travail et Études au Canada',
            'description' => 'Bienvenue chez Canadaim. Votre partenaire de confiance pour réussir votre projet d\'immigration au Canada. Explorez les services d\'immigration, travail, études et parrainage familial.',
            'keywords' => 'Canada immigration, études Canada, travail Canada, parrainage familial, visa canadien',
            'og_image' => '/img/home-hero.jpg',
        ],
        'immigration' => [
            'title' => 'Immigration au Canada - Programmes et Services | Canadaim',
            'description' => 'Explorez nos services d\'immigration au Canada. Programmes d\'immigration économique, parrainage familial, permis de travail et plus. Consultants agréés avec 10+ ans d\'expérience.',
            'keywords' => 'immigration Canada, programmes immigration, permis de travail, parrainage familial, visa immigration',
            'og_image' => '/img/immigration-hero.jpg',
        ],
        'travail' => [
            'title' => 'Opportunités d\'Emploi au Canada - Permis de Travail | Canadaim',
            'description' => 'Trouvez des opportunités d\'emploi au Canada. Services de placement, permis de travail, sponsorship employeur. Explorez les secteurs de l\'agriculture, construction, soins de santé et tech.',
            'keywords' => 'travail Canada, permis travail, emploi Canada, sponsorship employeur, salaires Canada',
            'og_image' => '/img/travail-hero.jpg',
        ],
        'etude' => [
            'title' => 'Études au Canada - Universités et Permis d\'Études | Canadaim',
            'description' => 'Étudiez au Canada. Aide à l\'admission universitaire, permis d\'études, bourses. Partenaires avec les universités canadiennes. Consultants expérimentés en éducation.',
            'keywords' => 'études Canada, université Canada, permis études, bourses Canada, éducation Canada',
            'og_image' => '/img/etude-hero.jpg',
        ],
        'sponsor' => [
            'title' => 'Parrainage Familial au Canada - Services de Sponsorship | Canadaim',
            'description' => 'Services de parrainage familial et sponsorship au Canada. Réunissez votre famille avec les programmes de parrainage. Consultants agréés ICCRC.',
            'keywords' => 'parrainage familial Canada, sponsorship Canada, réunification familiale, sponsor Canada',
            'og_image' => '/img/sponsor-hero.jpg',
        ],
        'a_propos' => [
            'title' => 'À Propos de Canadaim - Notre Mission et Équipe',
            'description' => 'Découvrez Canadaim. Cabinet de services d\'immigration avec 10+ ans d\'expérience. Consultants agréés ICCRC, accompagnement personnalisé, services de qualité.',
            'keywords' => 'immigration Canada, consultants immigration, ICCRC, services immigration professionnels',
            'og_image' => '/img/about-team.jpg',
        ],
        'contact' => [
            'title' => 'Contactez Canadaim - Bureau d\'Immigration | Formulaire de Contact',
            'description' => 'Contactez Canadaim. Tél: +1 (581) 222-5712. Formulaire de contact, horaires, localisation. Réponses rapides à vos questions sur l\'immigration canadienne.',
            'keywords' => 'contact Canadaim, immigration contact, formulaire contact, consultation immigration gratuite',
            'og_image' => '/img/contact-form.jpg',
        ],
    ];

    /**
     * Get SEO metadata for a specific page
     */
    public function getMetadata(string $page): array
    {
        return self::METADATA[$page] ?? self::METADATA['home'];
    }

    /**
     * Get all available pages metadata
     */
    public function getAllMetadata(): array
    {
        return self::METADATA;
    }

    /**
     * Get specific metadata field
     */
    public function getField(string $page, string $field): string
    {
        return self::METADATA[$page][$field] ?? '';
    }
}
