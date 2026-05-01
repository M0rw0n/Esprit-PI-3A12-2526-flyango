<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenTripMapService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $openTripMapApiKey = '')
    {
        $this->apiKey = $openTripMapApiKey ?: $_ENV['OPENTRIPMAP_API_KEY'] ?? '5ae2e3f221c38a28845f05b6302e1c70e5d0a6c0b9a6b6c0b9a6b6c0';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getPlaces(string $name, int $limit = 50): array
    {
        try {
            // 1. Get coordinates for the name
            $geoResponse = $this->getClient()->request('GET', "https://api.opentripmap.com/0.1/en/places/geoname", [
                'query' => [
                    'name' => $name,
                    'apikey' => $this->apiKey
                ]
            ]);
            $geoData = $geoResponse->toArray();

            if (!isset($geoData['lat'], $geoData['lon'])) {
                return $this->getMockData($name);
            }

            // 2. Search places nearby
            $placesResponse = $this->getClient()->request('GET', "https://api.opentripmap.com/0.1/en/places/radius", [
                'query' => [
                    'radius' => 20000, // Augmenté à 20km
                    'lon' => $geoData['lon'],
                    'lat' => $geoData['lat'],
                    'format' => 'json',
                    'limit' => $limit,
                    'apikey' => $this->apiKey
                ]
            ]);

            return $placesResponse->toArray();
        } catch (\Exception $e) {
            return $this->getMockData($name);
        }
    }

    private function getMockData(string $name): array
    {
        $places = [];
        $historicNames = [
            "Musée National du Bardo", "Médina de " . ucfirst($name), "Amphithéâtre d'El Jem",
            "Site Archéologique de Carthage", "Grande Mosquée de Kairouan", "Ribat de Monastir",
            "Synagogue de la Ghriba", "Fort de Kelibia", "Dougga", "Sbeitla", "Bulla Regia",
            "Thermes d'Antonin", "Port Punique", "Byrsa Hill", "Marché Central"
        ];
        
        $kinds = ['museums', 'historic', 'monuments', 'architecture', 'cultural'];

        // Base coordinates for the mock city
        $lat = 36.8065;
        $lon = 10.1815;

        $mockCoords = [
            'tunis' => [36.8065, 10.1815],
            'sfax' => [34.74, 10.76],
            'sousse' => [35.8254, 10.6406],
            'djerba' => [33.875, 10.85],
        ];

        foreach ($mockCoords as $city => $coords) {
            if (str_contains(mb_strtolower($name), $city)) {
                $lat = $coords[0];
                $lon = $coords[1];
                break;
            }
        }

        for ($i = 0; $i < 15; $i++) {
            $places[] = [
                'name' => $historicNames[$i % count($historicNames)],
                'kinds' => $kinds[rand(0, 2)] . ',' . $kinds[rand(3, 4)],
                'dist' => rand(500, 50000),
                'point' => [
                    'lat' => $lat + (rand(-100, 100) / 1000),
                    'lon' => $lon + (rand(-100, 100) / 1000)
                ]
            ];
        }

        return $places;
    }
}
