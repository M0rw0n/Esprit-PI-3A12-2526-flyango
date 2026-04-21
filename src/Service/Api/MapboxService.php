<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapboxService
{
    private ?HttpClientInterface $client = null;
    private string $accessToken;
    private string $publicToken;

    public function __construct(
        string $mapboxAccessToken = '',
        string $mapboxPublicToken = ''
    ) {
        $this->accessToken = $mapboxAccessToken ?: $_ENV['MAPBOX_ACCESS_TOKEN'] ?? getenv('MAPBOX_ACCESS_TOKEN') ?? '';
        $this->publicToken = $mapboxPublicToken ?: $_ENV['MAPBOX_PUBLIC_TOKEN'] ?? getenv('MAPBOX_PUBLIC_TOKEN') ?? 'pk.eyJ1IjoiZmx5YW5nbyIsImEiOiJjbTV...';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getPublicToken(): string
    {
        return $this->publicToken;
    }

    public function geocode(string $address): array
    {
        if (empty($this->accessToken)) {
            return $this->getMockGeocode($address);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json', [
                'query' => [
                    'access_token' => $this->accessToken,
                    'limit' => 1
                ]
            ]);

            $data = $response->toArray();
            
            if (!empty($data['features'])) {
                return [
                    'success' => true,
                    'lat' => $data['features'][0]['center'][1],
                    'lng' => $data['features'][0]['center'][0],
                    'place_name' => $data['features'][0]['place_name'],
                    'context' => $data['features'][0]['context'] ?? []
                ];
            }
            
            return ['success' => false, 'error' => 'No results found'];
        } catch (\Exception $e) {
            return $this->getMockGeocode($address);
        }
    }

    public function getStaticMapImage(string $center, int $width = 600, int $height = 400, int $zoom = 10): string
    {
        $token = $this->publicToken ?: 'pk.eyJ1IjoiZmx5YW5nbyIsImEiOiJjbTV...';
        return "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+1B3A6B($center)/$center,$zoom/{$width}x{$height}?access_token=$token";
    }

    public function getDirections(array $waypoints): array
    {
        if (empty($this->accessToken) || count($waypoints) < 2) {
            return $this->getMockDirections($waypoints);
        }

        $coordinates = implode(';', array_map(fn($p) => $p['lng'] . ',' . $p['lat'], $waypoints));

        try {
            $response = $this->getClient()->request('GET', "https://api.mapbox.com/directions/v5/mapbox/driving/$coordinates", [
                'query' => [
                    'access_token' => $this->accessToken,
                    'geometries' => 'geojson',
                    'overview' => 'full'
                ]
            ]);

            $data = $response->toArray();

            if ($data['code'] === 'Ok') {
                return [
                    'success' => true,
                    'distance' => $data['routes'][0]['distance'],
                    'duration' => $data['routes'][0]['duration'],
                    'geometry' => $data['routes'][0]['geometry']
                ];
            }

            return ['success' => false, 'error' => $data['code']];
        } catch (\Exception $e) {
            return $this->getMockDirections($waypoints);
        }
    }

    private function getMockGeocode(string $address): array
    {
        $mockCoords = [
            'tunis' => [36.8065, 10.1815],
            'djerba' => [33.875, 10.85],
            'sousse' => [35.8254, 10.6406],
            'hammamet' => [36.4, 10.6],
            'tozeur' => [33.92, 8.12],
            'kairouan' => [35.68, 10.11],
        ];

        $key = strtolower($address);
        foreach ($mockCoords as $city => $coords) {
            if (str_contains($key, $city)) {
                return [
                    'success' => true,
                    'lat' => $coords[0],
                    'lng' => $coords[1],
                    'place_name' => $address
                ];
            }
        }

        return [
            'success' => true,
            'lat' => 36.8065,
            'lng' => 10.1815,
            'place_name' => $address
        ];
    }

    private function getMockDirections(array $waypoints): array
    {
        if (count($waypoints) < 2) {
            return ['success' => false, 'error' => 'Need at least 2 waypoints'];
        }

        $totalDistance = 0;
        $totalDuration = 0;

        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $dist = rand(50000, 200000);
            $totalDistance += $dist;
            $totalDuration += ($dist / 50000) * 3600;
        }

        return [
            'success' => true,
            'distance' => $totalDistance,
            'duration' => $totalDuration,
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => array_map(fn($p) => [$p['lng'], $p['lat']], $waypoints)
            ]
        ];
    }
}