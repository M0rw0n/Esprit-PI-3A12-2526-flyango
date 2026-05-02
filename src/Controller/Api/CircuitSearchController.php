<?php

namespace App\Controller\Api;

use App\Repository\CircuitRepository;
use App\Service\SemanticSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/circuit')]
class CircuitSearchController extends AbstractController
{
    public function __construct(
        private CircuitRepository $circuitRepo
    ) {}

    #[Route('/search', name: 'api_circuit_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $pays = $request->query->get('pays', '');
        $lieu = $request->query->get('lieu', '');
        $prixMin = $request->query->get('prix_min', 0);
        $prixMax = $request->query->get('prix_max', 0);
        
        if (strlen($query) < 2 && empty($pays) && empty($lieu) && empty($prixMin) && empty($prixMax)) {
            return new JsonResponse([
                'success' => false,
                'message' => '至少需要一个搜索条件'
            ]);
        }

        try {
            $circuits = $this->circuitRepo->findAll();
            
            $results = [];
            foreach ($circuits as $circuit) {
                $match = true;
                $circuitText = mb_strtolower($circuit->getTitre() . ' ' . $circuit->getDescription() . ' ' . $circuit->getDestination());
                $circuitTextLower = mb_strtolower($circuitText);
                
                if (!empty($query)) {
                    $queryLower = mb_strtolower($query);
                    if (mb_stripos($circuitTextLower, $queryLower) === false) {
                        $match = false;
                    }
                }
                
                if ($match && !empty($pays)) {
                    $paysLower = mb_strtolower($pays);
                    if (mb_stripos($circuitTextLower, $paysLower) === false && mb_stripos($circuitTextLower, $paysLower) === false) {
                        $match = false;
                    }
                }
                
                if ($match && !empty($lieu)) {
                    $lieuLower = mb_strtolower($lieu);
                    if (mb_stripos($circuitTextLower, $lieuLower) === false) {
                        $match = false;
                    }
                }
                
                if ($match && !empty($prixMax) && $circuit->getPrix() > $prixMax) {
                    $match = false;
                }
                
                if ($match && !empty($prixMin) && $circuit->getPrix() < $prixMin) {
                    $match = false;
                }
                
                if ($match) {
                    $results[] = [
                        'circuit' => [
                            'id' => $circuit->getId(),
                            'titre' => $circuit->getTitre(),
                            'description' => $circuit->getDescription() ?? '',
                            'villeDepart' => $circuit->getDestination() ?? '',
                            'duree' => $circuit->getDuree() ?? '',
                            'prix' => $circuit->getPrix() ?? null,
                            'imagePath' => $circuit->getImage() ?? null,
                        ],
                        'score' => 1
                    ];
                }
            }
            
            return new JsonResponse([
                'success' => true,
                'query' => $query,
                'filters' => [
                    'pays' => $pays,
                    'lieu' => $lieu,
                    'prix_min' => $prixMin,
                    'prix_max' => $prixMax
                ],
                'results' => $results,
                'count' => count($results)
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/search/examples', name: 'api_circuit_search_examples', methods: ['GET'])]
    public function getExamples(): JsonResponse
    {
        $examples = [
            "je veux un voyage romantique pas cher en mer avec plage calme",
            "vacances luxe détente spa",
            "voyage aventure nature",
            "circuit famille avec enfants",
            "randonnée montagne découverte",
            "voyage culturel historique",
            "séjour romantique bord de mer",
            "aventure desert et oasis",
        ];

        return new JsonResponse([
            'success' => true,
            'examples' => $examples
        ]);
    }
}