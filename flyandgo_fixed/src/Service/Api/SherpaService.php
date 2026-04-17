<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SherpaService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $sherpaApiKey = '')
    {
        $this->apiKey = $sherpaApiKey ?: $_ENV['SHERPA_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create([
                'base_uri' => 'https://api.joinsherpa.com/v2/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);
        }
        return $this->client;
    }

    private const VISA_FREE_FOR_TN = [
        'turquie', 'turkey', 'tr', 'istanbul', 'ankara',
        'maroc', 'morocco', 'ma', 'casablanca', 'rabat', 'marrakech',
        'algérie', 'algeria', 'dz', 'alger', 'oran',
        'libye', 'libya', 'ly', 'tripoli',
        'mauritanie', 'mauritania', 'mr', 'nouakchott',
        'sénégal', 'senegal', 'sn', 'dakar',
        'japon', 'japan', 'jp', 'tokyo', 'osaka',
        'corée du sud', 'south korea', 'kr', 'seoul',
        'brésil', 'brazil', 'br', 'rio', 'sao paulo',
        'tunisie', 'tunisia', 'tn', 'hammamet', 'sousse', 'djerba', 'tozeur', 'kairouan'
    ];

    private const VISA_REQUIRED_FOR_TN = [
        'france', 'fr', 'paris', 'lyon', 'marseille',
        'italie', 'italy', 'it', 'rome', 'milan',
        'espagne', 'spain', 'es', 'madrid', 'barcelone',
        'allemagne', 'germany', 'de', 'berlin', 'munich',
        'états-unis', 'usa', 'us', 'new york', 'washington',
        'royaume-uni', 'uk', 'gb', 'londres', 'london',
        'canada', 'ca', 'montreal', 'toronto',
        'chine', 'china', 'cn', 'pekin', 'shanghai',
        'égypte', 'egypt', 'eg', 'le caire', 'cairo',
        'emirats', 'uae', 'ae', 'dubai', 'abu dhabi'
    ];

    public function getRequirements(string $origin, string $destination, string $nationality = 'TN'): array
    {
        $visaInfo = $this->checkVisaRequirement($destination, $nationality);
        $healthInfo = $this->checkHealthRequirement($destination);

        if (empty($this->apiKey)) {
            return [
                'success' => true,
                'origin' => $origin,
                'destination' => $destination,
                'visa' => $visaInfo['message'],
                'visa_required' => $visaInfo['required'],
                'health' => $healthInfo
            ];
        }

        try {
            // Real API call
            $response = $this->getClient()->request('GET', 'requirements', [
                'query' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'nationality' => $nationality
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'origin' => $origin,
                'destination' => $destination,
                'visa' => $data['visa_info'] ?? $visaInfo['message'],
                'visa_required' => $data['visa_required'] ?? $visaInfo['required'],
                'health' => $data['health_info'] ?? $healthInfo
            ];
        } catch (\Exception $e) {
            return [
                'success' => true,
                'origin' => $origin,
                'destination' => $destination,
                'visa' => $visaInfo['message'],
                'visa_required' => $visaInfo['required'],
                'health' => $healthInfo,
                'error' => $e->getMessage()
            ];
        }
    }

    private function checkVisaRequirement(string $destination, string $nationality): array
    {
        $dest = mb_strtolower(trim($destination));
        $nat = mb_strtolower(trim($nationality));

        if ($nat === 'tn') {
            // Voyage intérieur ou pays sans visa
            foreach (self::VISA_FREE_FOR_TN as $free) {
                if (str_contains($dest, $free)) {
                    return [
                        'required' => false,
                        'message' => '✅ Aucun visa requis pour les Tunisiens (Zone libre ou VOA).'
                    ];
                }
            }

            // Pays avec visa
            foreach (self::VISA_REQUIRED_FOR_TN as $req) {
                if (str_contains($dest, $req)) {
                    return [
                        'required' => true,
                        'message' => '⚠️ Visa obligatoire requis pour cette destination.'
                    ];
                }
            }
        }

        return [
            'required' => true,
            'message' => 'ℹ️ Vérifiez les exigences de visa (généralement requis).'
        ];
    }

    private function checkHealthRequirement(string $destination): string
    {
        $dest = mb_strtolower(trim($destination));
        
        if (str_contains($dest, 'tunisie') || str_contains($dest, 'tunisia') || str_contains($dest, 'tn')) {
            return '✅ Aucune restriction sanitaire particulière.';
        }

        if (str_contains($dest, 'sénégal') || str_contains($dest, 'senegal') || str_contains($dest, 'dakar')) {
            return '💉 Vaccination Fièvre Jaune recommandée.';
        }

        return '✅ Aucune restriction majeure, prévoyez une assurance voyage.';
    }

    /**
     * Get requirements for a full circuit itinerary
     */
    public function getCircuitRequirements(array $waypoints, string $nationality = 'TN'): array
    {
        $requirements = [];
        if (count($waypoints) < 2) return $requirements;

        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $origin = $waypoints[$i];
            $destination = $waypoints[$i + 1];
            
            // In a real scenario, we'd check if this segment crosses a border
            // For now, we call the API for each segment
            $requirements[] = $this->getRequirements($origin, $destination, $nationality);
        }

        return $requirements;
    }
}
