<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    // Articles de contenu indexés pour la recherche
    private function getSearchableContent(): array
    {
        return [
            [
                'id' => 'immigration-rp',
                'title' => 'Résidence Permanente',
                'category' => 'Immigration',
                'content' => 'Accédez à la résidence permanente canadienne par plusieurs programmes: Entrée Express, Programmes provinciaux PNP',
                'url' => $this->generateUrl('immigration'),
                'keywords' => ['résidence', 'permanente', 'immigration', 'entrée express', 'pnp']
            ],
            [
                'id' => 'immigration-visa',
                'title' => 'Visa de Travail',
                'category' => 'Immigration',
                'content' => 'Obtenez un visa de travail temporaire: IMP, Permis de travail fermé, Permis de travail ouvert',
                'url' => $this->generateUrl('immigration'),
                'keywords' => ['visa', 'travail', 'temporaire', 'imp', 'permis']
            ],
            [
                'id' => 'travail-secteurs',
                'title' => 'Secteurs en Demande',
                'category' => 'Travail',
                'content' => 'Technologies, Santé, Construction & Ingénierie, Affaires & Finance',
                'url' => $this->generateUrl('travail'),
                'keywords' => ['emploi', 'travail', 'technologies', 'santé', 'construction', 'finance']
            ],
            [
                'id' => 'etude-programmes',
                'title' => 'Programmes d\'Études',
                'category' => 'Étude',
                'content' => 'Baccalauréat, Maîtrise, Doctorat, Programmes collégiaux',
                'url' => $this->generateUrl('etude'),
                'keywords' => ['étude', 'études', 'université', 'collège', 'baccalauréat', 'maîtrise', 'doctorat']
            ],
            [
                'id' => 'etude-bourses',
                'title' => 'Bourses d\'Études',
                'category' => 'Étude',
                'content' => 'Plusieurs bourses d\'études sont disponibles pour les étudiants internationaux au Canada',
                'url' => $this->generateUrl('etude'),
                'keywords' => ['bourses', 'finance', 'aide', 'étudiants', 'international']
            ],
            [
                'id' => 'sponsor-familial',
                'title' => 'Parrainage Familial',
                'category' => 'Sponsor',
                'content' => 'Parrainez vos proches: conjoint, enfants, parents, grands-parents, frères, sœurs',
                'url' => $this->generateUrl('sponsor'),
                'keywords' => ['sponsor', 'parrainage', 'familial', 'famille', 'conjoint', 'enfants', 'parents']
            ],
            [
                'id' => 'apropos-mission',
                'title' => 'Notre Mission',
                'category' => 'À Propos',
                'content' => 'Fournir des services d\'immigration professionnels, transparents et abordables',
                'url' => $this->generateUrl('a_propos'),
                'keywords' => ['mission', 'vision', 'valeurs', 'intégrité', 'excellence']
            ],
            [
                'id' => 'contact-info',
                'title' => 'Nous Contacter',
                'category' => 'Contact',
                'content' => 'Téléphone: +1 (581) 222-5712, Email: info@canadaim.com, Adresse: 10 rue des lilas Ouest',
                'url' => $this->generateUrl('contact'),
                'keywords' => ['contact', 'téléphone', 'email', 'adresse', 'heures', 'ouverture']
            ],
            [
                'id' => 'home-bienvenue',
                'title' => 'Bienvenue sur Canadaim',
                'category' => 'Accueil',
                'content' => 'Votre partenaire de confiance pour l\'immigration, l\'emploi et les études au Canada',
                'url' => $this->generateUrl('home'),
                'keywords' => ['accueil', 'bienvenue', 'canada', 'immigration', 'emploi', 'études']
            ],
        ];
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $results = [];

        if (!empty($query)) {
            $query_lower = strtolower($query);
            $searchable = $this->getSearchableContent();

            foreach ($searchable as $item) {
                $title_match = stripos($item['title'], $query) !== false;
                $content_match = stripos($item['content'], $query) !== false;
                $keyword_match = false;

                foreach ($item['keywords'] as $keyword) {
                    if (stripos($keyword, $query) === 0 || stripos($keyword, $query_lower) !== false) {
                        $keyword_match = true;
                        break;
                    }
                }

                if ($title_match || $content_match || $keyword_match) {
                    $relevance = 0;
                    if ($title_match) $relevance += 3;
                    if ($keyword_match) $relevance += 2;
                    if ($content_match) $relevance += 1;

                    $item['relevance'] = $relevance;
                    $results[] = $item;
                }
            }

            // Trier par pertinence
            usort($results, function($a, $b) {
                return $b['relevance'] <=> $a['relevance'];
            });
        }

        return $this->render('search/results.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
