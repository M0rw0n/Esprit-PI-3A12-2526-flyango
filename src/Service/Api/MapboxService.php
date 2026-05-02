<?php

namespace App\Service\Api;

use App\Entity\Circuit;
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

    public function getStaticMapWithRoute(array $waypoints, int $width = 800, int $height = 600): string
    {
        if (empty($waypoints)) return '';
        
        $token = $this->publicToken;
        
        // Resolve coordinates if they are strings (city names)
        $resolvedWaypoints = [];
        foreach ($waypoints as $wp) {
            if (is_string($wp)) {
                $geo = $this->geocode($wp);
                if ($geo['success']) {
                    $resolvedWaypoints[] = ['lat' => $geo['lat'], 'lng' => $geo['lng']];
                }
            } else if (isset($wp['lat'], $wp['lng'])) {
                $resolvedWaypoints[] = $wp;
            }
        }

        if (empty($resolvedWaypoints)) return '';

        // Instagram style: Use outdoors style, thicker line, and markers for start/end
        $start = $resolvedWaypoints[0];
        $end = $resolvedWaypoints[count($resolvedWaypoints) - 1];
        
        $path = urlencode(json_encode([
            'type' => 'Feature',
            'properties' => [
                'stroke' => '#E1306C', // Instagram pink/red
                'stroke-width' => 6,
                'stroke-opacity' => 0.8
            ],
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => array_map(fn($p) => [$p['lng'], $p['lat']], $resolvedWaypoints)
            ]
        ]));

        $markers = "pin-s-a+E1306C({$start['lng']},{$start['lat']}),pin-s-b+1B3A6B({$end['lng']},{$end['lat']})";

        // Using mapbox/outdoors-v12 for a better look
        return "https://api.mapbox.com/styles/v1/mapbox/outdoors-v12/static/$markers,geojson($path)/auto/{$width}x{$height}@2x?access_token=$token";
    }

    public function getStorytellingConfig(Circuit $circuit): array
    {
        $stops = $circuit->getStops() ?: [];
        $chapters = [];

        // Add Departure as first chapter
        if ($circuit->getDepart()) {
            $geo = $this->geocode($circuit->getDepart());
            if ($geo['success']) {
                $chapters[] = [
                    'id' => 'chapter-start',
                    'alignment' => 'left',
                    'title' => 'Départ : ' . $circuit->getDepart(),
                    'description' => 'Le début de votre aventure commence ici.',
                    'location' => [
                        'center' => [$geo['lng'], $geo['lat']],
                        'zoom' => 10,
                        'pitch' => 45,
                        'bearing' => 0
                    ]
                ];
            }
        }

        foreach ($stops as $index => $stop) {
            $lat = 0; $lng = 0; $name = 'Étape ' . ($index + 1); $desc = '';

            if (is_string($stop)) {
                $geo = $this->geocode($stop);
                if ($geo['success']) {
                    $lat = $geo['lat'];
                    $lng = $geo['lng'];
                    $name = $stop;
                }
            } else {
                $lat = $stop['lat'] ?? 0;
                $lng = $stop['lng'] ?? 0;
                $name = $stop['name'] ?? $name;
                $desc = $stop['description'] ?? '';
            }

            if ($lat !== 0) {
                $chapters[] = [
                    'id' => 'chapter-' . $index,
                    'alignment' => $index % 2 === 0 ? 'right' : 'left',
                    'title' => $name,
                    'description' => $desc ?: 'Découvrez cette étape magnifique du circuit.',
                    'location' => [
                        'center' => [$lng, $lat],
                        'zoom' => 13,
                        'pitch' => 60,
                        'bearing' => rand(-20, 20)
                    ]
                ];
            }
        }

        // Add Destination as final chapter if not already there
        if ($circuit->getDestination() && $circuit->getDestination() !== $circuit->getDepart()) {
            $geo = $this->geocode($circuit->getDestination());
            if ($geo['success']) {
                $chapters[] = [
                    'id' => 'chapter-end',
                    'alignment' => 'center',
                    'title' => 'Destination : ' . $circuit->getDestination(),
                    'description' => 'L\'aboutissement de votre voyage mémorable.',
                    'location' => [
                        'center' => [$geo['lng'], $geo['lat']],
                        'zoom' => 11,
                        'pitch' => 30,
                        'bearing' => 0
                    ]
                ];
            }
        }

        return [
            'style' => 'mapbox://styles/mapbox/outdoors-v12',
            'accessToken' => $this->publicToken,
            'showMarkers' => true,
            'theme' => 'light',
            'chapters' => $chapters
        ];
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
            'sfax' => [34.74, 10.76],
            'monastir' => [35.77, 10.82],
            'bizerte' => [37.27, 9.87],
            'tabarka' => [36.95, 8.75],
            'paris' => [48.8566, 2.3522],
            'london' => [51.5074, -0.1278],
            'new york' => [40.7128, -74.0060],
            'istanbul' => [41.0082, 28.9784],
            'marrakech' => [31.6295, -7.9811],
            'casablanca' => [33.5731, -7.5898],
            'alger' => [36.7525, 3.0420],
        ];

        $key = mb_strtolower(trim($address));
        foreach ($mockCoords as $city => $coords) {
            if (str_contains($key, $city)) {
                return [
                    'success' => true,
                    'lat' => $coords[0],
                    'lng' => $coords[1],
                    'place_name' => ucfirst($city) . ', ' . (in_array($city, ['paris', 'london', 'new york', 'istanbul', 'marrakech', 'casablanca', 'alger']) ? 'Monde' : 'Tunisie')
                ];
            }
        }

        // Default to Tunis if no match
        return [
            'success' => true,
            'lat' => 36.8065,
            'lng' => 10.1815,
            'place_name' => $address . ' (Position estimée)'
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