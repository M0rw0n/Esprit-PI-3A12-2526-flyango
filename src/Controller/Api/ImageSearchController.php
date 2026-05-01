<?php

namespace App\Controller\Api;

use App\Service\OpenAIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

#[Route('/api/image')]
class ImageSearchController extends AbstractController
{
    private const COUNTRIES = [
        'Tunisie', 'Maroc', 'Algérie', 'Égypte', 'Turquie', 'France', 'Espagne', 'Italie',
        'Grèce', 'Portugal', 'Allemagne', 'Angleterre', 'Belgique', 'Suisse', 'Autriche',
        'Chine', 'Japon', 'Thaïlande', 'Vietnam', 'Inde', 'Indonésie', 'Malaisie', 'Philippines',
        'États-Unis', 'Canada', 'Brésil', 'Mexique', 'Argentine', 'Chili', 'Pérou',
        'Australie', 'Nouvelle-Zélande', 'Afrique du Sud', 'Kenya', 'Sénégal', 'Gambie'
    ];

    private const COUNTRY_TAGS = [
        'Tunisie' => ['sahara', 'djerba', 'tunis', 'medina', 'carthage', 'palme', 'djellaba', 'KSar'],
        'Maroc' => ['marrakech', 'marrakesh', 'fes', 'meknes', 'erg', 'riad', 'atlas', 'medina'],
        'Égypte' => ['cairo', 'pyramid', 'gizeh', 'sphinx', 'nile', 'pharaon', 'louxor'],
        'Turquie' => ['istanbul', 'istanboul', 'mosque', 'cupole', 'hagia', 'effet', 'pamukkale', 'capadoce'],
        'France' => ['paris', 'tour', 'eiffel', 'loire', 'louvre', 'notre'],
        'Italie' => ['rome', 'rome', 'colosseo', 'venice', 'florence', 'pise', 'vatican'],
        'Espagne' => ['barcelona', 'madrid', 'alhambra', 'grenade', 'sagrada'],
        'Grèce' => ['athens', 'athènes', 'santorin', 'mykonos', 'parthénon', 'acropole', 'crete'],
        'Portugal' => ['lisbon', 'porto', 'algarve'],
        'Japon' => ['tokyo', 'kyoto', 'temple', 'shrine', 'fuji', 'cherry'],
        'Chine' => ['beijing', 'shanghai', 'great wall', 'forbidden', 'terracotta'],
        'Thaïlande' => ['bangkok', 'temple', 'watt', 'phuket', 'chiang'],
        'Vietnam' => ['hanoi', 'hcm', 'halong', 'tunnel'],
        'Inde' => ['delhi', 'mumbai', 'taj', 'mahal', 'varanasi', 'kerala'],
        'États-Unis' => ['new york', 'usa', 'las vegas', 'grand canyon', 'hollywood'],
        'Canada' => ['toronto', 'montreal', 'banff', 'niagara'],
        'Brésil' => ['rio', 'brasilia', 'copacabana', 'christ'],
        'Australie' => ['sydney', 'opera', 'barrier', 'uluru', 'ayers'],
    ];

    #[Route('/search', name: 'image_search', methods: ['POST'])]
    public function search(Request $request, OpenAIService $openAI): JsonResponse
    {
        $imageData = $request->request->get('image');
        
        if (!$imageData) {
            return $this->json(['success' => false, 'error' => 'Aucune image fournie'], 400);
        }

        if (!$openAI->isEnabled()) {
            return $this->fallbackImageSearch($imageData);
        }

        try {
            $httpClient = HttpClient::create(['timeout' => 30]);
            
            $response = $httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $openAI->getApiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Analyse cette image et identifie le pays le plus probable. Réponds uniquement avec le nom du pays en anglais ou en français.'
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $imageData
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'max_tokens' => 50,
                ],
            ]);

            $data = $response->toArray();
            
            if (isset($data['choices'][0]['message']['content'])) {
                $country = trim($data['choices'][0]['message']['content']);
                
                return $this->json([
                    'success' => true,
                    'country' => $country,
                    'original' => $country,
                ]);
            }

            return $this->json(['success' => false, 'error' => 'Réponse invalide'], 500);

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'rate limit')) {
                return $this->fallbackImageSearch($imageData);
            }
            return $this->json(['success' => false, 'error' => $errorMsg], 500);
        }
    }

    private function fallbackImageSearch(string $imageData): JsonResponse
    {
        return $this->json([
            'success' => false,
            'error' => 'Limite OpenAI atteinte. Cliquez sur 📷 puis utilisez Google Lens manuellement.',
            'fallback' => true,
            'alternative' => true,
        ]);
    }
}