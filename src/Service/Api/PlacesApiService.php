<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlacesApiService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $googlePlacesApiKey = '')
    {
        $this->apiKey = $googlePlacesApiKey ?: $_ENV['GOOGLE_PLACES_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchNearbyPlaces(float $lat, float $lng, string $type = '', int $radius = 5000, string $keyword = ''): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockNearbyPlaces($lat, $lng, $type);
        }

        try {
            $params = [
                'location' => "$lat,$lng",
                'radius' => $radius,
                'key' => $this->apiKey
            ];

            if ($type) $params['type'] = $type;
            if ($keyword) $params['keyword'] = $keyword;

            $response = $this->getClient()->request('GET', 'https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'query' => $params
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'OK') {
                return [
                    'success' => true,
                    'places' => $this->parsePlaces($data['results'])
                ];
            }
            return ['success' => false, 'error' => $data['status']];
        } catch (\Exception $e) {
            return $this->getMockNearbyPlaces($lat, $lng, $type);
        }
    }

    public function searchText(string $query, string $location = '', string $type = ''): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockSearchResults($query);
        }

        try {
            $params = [
                'query' => $query,
                'key' => $this->apiKey
            ];

            if ($location) $params['location'] = $location;
            if ($type) $params['type'] = $type;

            $response = $this->getClient()->request('GET', 'https://maps.googleapis.com/maps/api/place/textsearch/json', [
                'query' => $params
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'OK') {
                return [
                    'success' => true,
                    'places' => $this->parsePlaces($data['results'])
                ];
            }
            return ['success' => false, 'error' => $data['status']];
        } catch (\Exception $e) {
            return $this->getMockSearchResults($query);
        }
    }

    public function getPlacePhoto(string $photoReference, int $maxWidth = 400): string
    {
        if (empty($this->apiKey)) {
            return '';
        }

        return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=$maxWidth&photo_reference=$photoReference&key={$this->apiKey}";
    }

    private function parsePlaces(array $results): array
    {
        $places = [];
        foreach ($results as $place) {
            $places[] = [
                'id' => $place['place_id'] ?? '',
                'name' => $place['name'] ?? '',
                'address' => $place['vicinity'] ?? $place['formatted_address'] ?? '',
                'lat' => $place['geometry']['location']['lat'] ?? 0,
                'lng' => $place['geometry']['location']['lng'] ?? 0,
                'rating' => $place['rating'] ?? 0,
                'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                'types' => $place['types'] ?? [],
                'icon' => $place['icon'] ?? '',
                'photo_reference' => $place['photos'][0]['photo_reference'] ?? null
            ];
        }
        return $places;
    }

    private function getMockNearbyPlaces(float $lat, float $lng, string $type): array
    {
        $types = [
            'restaurant' => ['Le Petit Café', 'La Brasserie', 'Le Gourmet'],
            'museum' => ['Musée Local', 'Galerie d\'Art', 'Musée Histoire'],
            'park' => ['Parc Central', 'Jardin Public', 'Parc Municipal'],
            'hotel' => ['Hôtel Premier', 'Hôtel Confort', 'Hôtel Luxe'],
            'default' => ['Lieu Interessant 1', 'Lieu Interessant 2', 'Lieu Interessant 3']
        ];

        $names = $types[$type] ?? $types['default'];

        $places = [];
        foreach ($names as $index => $name) {
            $places[] = [
                'id' => 'mock_place_' . $index,
                'name' => $name,
                'address' => '123 Rue Principale, Ville',
                'lat' => $lat + (rand(-100, 100) / 10000),
                'lng' => $lng + (rand(-100, 100) / 10000),
                'rating' => round(rand(35, 50) / 10, 1),
                'user_ratings_total' => rand(10, 500),
                'types' => [$type ?: 'point_of_interest'],
                'icon' => '',
                'photo_reference' => null
            ];
        }

        return ['success' => true, 'places' => $places];
    }

    private function getMockSearchResults(string $query): array
    {
<<<<<<< HEAD
        $dest = mb_strtolower(trim($query));
        $lat = 36.8065;
        $lng = 10.1815;

        $mockCoords = [
            'tunis' => [36.8065, 10.1815],
            'sfax' => [34.74, 10.76],
            'sousse' => [35.8254, 10.6406],
            'djerba' => [33.875, 10.85],
            'hammamet' => [36.4, 10.6],
            'paris' => [48.8566, 2.3522],
            'london' => [51.5074, -0.1278],
        ];

        foreach ($mockCoords as $city => $coords) {
            if (str_contains($dest, $city)) {
                $lat = $coords[0];
                $lng = $coords[1];
                break;
            }
        }

=======
>>>>>>> testsisi
        return [
            'success' => true,
            'places' => [
                [
                    'id' => 'mock_1',
<<<<<<< HEAD
                    'name' => "Top Attraction in " . ucfirst($query),
                    'address' => 'Centre ville, ' . ucfirst($query),
                    'lat' => $lat + 0.001,
                    'lng' => $lng + 0.001,
                    'rating' => 4.8,
                    'user_ratings_total' => 1250,
                    'types' => ['tourist_attraction'],
                    'icon' => '',
                    'photo_reference' => null
                ],
                [
                    'id' => 'mock_2',
                    'name' => "Musée Historique de " . ucfirst($query),
                    'address' => 'Quartier culturel, ' . ucfirst($query),
                    'lat' => $lat - 0.002,
                    'lng' => $lng + 0.002,
                    'rating' => 4.5,
                    'user_ratings_total' => 850,
                    'types' => ['museum'],
=======
                    'name' => "$query - Location 1",
                    'address' => 'Address for ' . $query,
                    'lat' => 48.8566,
                    'lng' => 2.3522,
                    'rating' => 4.2,
                    'user_ratings_total' => 125,
                    'types' => ['point_of_interest'],
>>>>>>> testsisi
                    'icon' => '',
                    'photo_reference' => null
                ]
            ]
        ];
    }
}