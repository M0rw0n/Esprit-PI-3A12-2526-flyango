<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TravelPreparationAssistantService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private array $cache = [];
    private int $cacheExpiry = 3600;

    public function __construct(
        ?string $openAiApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->apiKey = $openAiApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_key_here';
    }
    
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    private function getCacheKey(string $hotel, string $ville, string $pays): string
    {
        return md5(strtolower($hotel) . '|' . strtolower($ville) . '|' . strtolower($pays));
    }

    private function getFromCache(string $key): ?array
    {
        if (isset($this->cache[$key]) && $this->cache[$key]['expires'] > time()) {
            return $this->cache[$key]['data'];
        }
        return null;
    }

    private function saveToCache(string $key, array $data): void
    {
        $this->cache[$key] = [
            'data' => $data,
            'expires' => time() + $this->cacheExpiry
        ];
    }

    public function generateAdvice(array $accommodationData): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured'
            ];
        }

        $prompt = $this->buildPrompt($accommodationData);

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ],
                'verify_peer' => false,
                'verify_host' => false,
            ]);

            $data = $response->toArray();

            if (isset($data['choices'][0]['message']['content'])) {
                $content = trim($data['choices'][0]['message']['content']);
                $advice = $this->parseAdvice($content);

                return [
                    'success' => true,
                    'advice' => $advice,
                    'raw_response' => $content
                ];
            }

            return [
                'success' => false,
                'error' => 'Unexpected response format'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Tu es l'assistant voyage expert de Fly&Go. Ton rôle est de générer des conseils pratiques et personnalisés pour les voyageurs.

Analyse les informations fournies sur l'hébergement et le voyage, puis génère une liste de conseils utiles.
Chaque conseil doit être:
- Pratique et actionnable
- Relié aux équipements, services, localisation ou saison du séjour
- Concis (1-2 phrases maximum)

Catégories de conseils possibles:
- Équipements à apporter (maillot de bain, crème solaire, etc.)
- Vêtements adaptés à la saison
- Transports locaux (parking, transports publics, navette)
- Activités recommandées selon la proximité
- Astuces pratiques selon le type d'hébergement

Format de réponse STRICT:
Retourne EXACTEMENT une liste de conseils, un par ligne, sans.numéro, sans bullet point, sans emoji dans les réponses.
 Chaque ligne doit commencer par un tiret "- " suivi du conseil.

Exemples de format:
- Apportez un maillot de bain : la piscine est à votre disposition
- La crème solaire est essentielle : la plage est à proximité
- Prévoyez des vêtements chauds : le séjour est en hiver
- Optez pour les transports publics : aucun parking n'est disponible sur place
- Portez des chaussures confortables : le centre-ville est accessible à pied

Ne mets PAS de tiret pour les informations générales comme le nom de l'hébergement, la ville ou le pays.
PROMPT;
    }

    private function buildPrompt(array $data): string
    {
        $name = $data['name'] ?? 'Inconnu';
        $city = $data['city'] ?? 'Inconnue';
        $country = $data['country'] ?? 'Inconnu';
        $season = $data['season'] ?? 'Non précisée';
        $accommodationType = $data['accommodation_type'] ?? 'Non précisé';
        $equipment = $data['equipment'] ?? [];
        $services = $data['services'] ?? [];
        $location = $data['location'] ?? 'Non précisée';
        $nearBeach = $data['near_beach'] ?? false;
        $hasParking = $data['has_parking'] ?? true;
        $hasPool = $data['has_pool'] ?? false;

        $equipmentStr = implode(', ', $equipment);
        $servicesStr = implode(', ', $services);

        $prompt = <<<INFO
Hébergement: $name
Ville: $city
Pays: $country
Saison: $season
Type d'hébergement: $accommodationType
Équipements: $equipmentStr
Services: $servicesStr
Localisation: $location
Proche de la plage: {($nearBeach ? 'Oui' : 'Non')}
Parking: {($hasParking ? 'Oui' : 'Non')}
Piscine: {($hasPool ? 'Oui' : 'Non')}
INFO;

        return $prompt;
    }

    private function parseAdvice(string $content): array
    {
        $lines = explode("\n", $content);
        $advice = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $line = ltrim($line, '-•* ');
            if (
                stripos($line, 'hébergement') !== false ||
                stripos($line, 'ville') !== false ||
                stripos($line, 'pays') !== false ||
                strlen($line) < 10
            ) continue;
            $advice[] = $line;
        }

        return $advice;
    }

    public function generateAdviceFromName(string $hotelName, ?string $ville = null, ?string $pays = 'Tunisie', ?string $type = 'hôtel'): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured'
            ];
        }

        $cacheKey = $this->getCacheKey($hotelName, $ville ?? '', $pays);
        $cached = $this->getFromCache($cacheKey);
        if ($cached) {
            return $cached;
        }

        $month = (int)date('n');
        $season = match(true) {
            $month >= 3 && $month <= 5 => 'printemps',
            $month >= 6 && $month <= 8 => 'été',
            $month >= 9 && $month <= 11 => 'automne',
            default => 'hiver',
        };

        $locationInfo = $ville ?? 'Non précisée';

        $prompt = "Génère des conseils pratiques pour un séjour à l'hôtel \"$hotelName\" à $locationInfo, $pays.\n";
        $prompt .= "Saison actuelle: $season\n";
        $prompt .= "Type: $type\n";
        $prompt .= "\nAnalyse le contexte et génère des conseils personnalisés selon:\n";
        $prompt .= "- La saison et le climat local\n";
        $prompt .= "- Le type d'hébergement\n";
        $prompt .= "- Les activités typiques de la région\n";

        try {
            $apiKey = $this->apiKey;
            
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu généres des conseils courts pour les voyageurs.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 500,
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray();

            if (isset($data['choices'][0]['message']['content'])) {
                $content = trim($data['choices'][0]['message']['content']);
                $advice = $this->parseAdvice($content);

                $result = [
                    'success' => true,
                    'advice' => $advice,
                    'hotel' => $hotelName,
                    'city' => $ville,
                    'season' => $season
                ];
                
                $this->saveToCache($cacheKey, $result);
                return $result;
            }

            $result = [
                'success' => false,
                'error' => 'Unexpected response format'
            ];
            
            $this->saveToCache($cacheKey, $result);
            return $result;

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            
            if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'rate limit')) {
                return [
                    'success' => false,
                    'error' => 'Trop de requêtes. Veuillez patienter quelques secondes et réessayer.',
                    'retry' => true
                ];
            }
            
            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Erreur inconnue'
        ];
    }
    
    public function generateFallbackAdvice(string $hotelName, ?string $ville = null, string $pays = 'Tunisie'): array
    {
        $month = (int)date('n');
        $isSummer = $month >= 6 && $month <= 8;
        $isWinter = $month >= 12 || $month <= 2;
        $season = $isSummer ? 'été' : ($isWinter ? 'hiver' : 'printemps/automne');
        
        $advice = [];
        $advice[] = "Consultez le site officiel de l'hébergement pour les équipements disponibles.";
        
        if ($pays === 'Tunisie') {
            if ($isSummer) {
                $advice[] = "Apportez de la crème solaire et des lunettes de soleil.";
                $advice[] = "Prévoyez des vêtements légers et呼吸ants.";
            } elseif ($isWinter) {
                $advice[] = "Emportez des vêtements chauds pour les soirées.";
            }
            $advice[] = "Les taxis et locations de voitures sont disponibles sur place.";
        } elseif ($pays === 'France') {
            if ($isSummer) {
                $advice[] = "Préparez-vous pour les longues soirées d'été.";
            } else {
                $advice[] = "Vérifiez les horaires des transports en commun.";
            }
        } else {
            $advice[] = "Renseignez-vous sur les modalités d'arrivée à l'aéroport.";
            $advice[] = "Ayez une connexion internet pour vos réservations.";
        }
        
        return [
            'success' => true,
            'advice' => $advice,
            'hotel' => $hotelName,
            'city' => $ville,
            'season' => $season,
            'fallback' => true
        ];
    }
}