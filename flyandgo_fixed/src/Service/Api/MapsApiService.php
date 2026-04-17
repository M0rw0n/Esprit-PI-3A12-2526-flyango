<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapsApiService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $googleMapsApiKey = '')
    {
        $this->apiKey = $googleMapsApiKey ?: $_ENV['GOOGLE_MAPS_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getCoordinates(string $address): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockCoordinates($address);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address' => $address,
                    'key' => $this->apiKey
                ]
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'OK') {
                return [
                    'success' => true,
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address']
                ];
            }
            return ['success' => false, 'error' => $data['status']];
        } catch (\Exception $e) {
            return $this->getMockCoordinates($address);
        }
    }

    public function getPlaceDetails(string $placeId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockPlaceDetails();
        }

        try {
            $response = $this->getClient()->request('GET', 'https://maps.googleapis.com/maps/api/place/details/json', [
                'query' => [
                    'place_id' => $placeId,
                    'key' => $this->apiKey
                ]
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'OK') {
                return [
                    'success' => true,
                    'name' => $data['result']['name'] ?? '',
                    'address' => $data['result']['formatted_address'] ?? '',
                    'rating' => $data['result']['rating'] ?? 0,
                    'reviews' => $data['result']['reviews'] ?? [],
                    'photos' => $data['result']['photos'] ?? [],
                    'opening_hours' => $data['result']['opening_hours'] ?? null,
                    'website' => $data['result']['website'] ?? '',
                    'phone' => $data['result']['formatted_phone_number'] ?? ''
                ];
            }
            return ['success' => false, 'error' => $data['status']];
        } catch (\Exception $e) {
            return $this->getMockPlaceDetails();
        }
    }

    public function getDirections(string $origin, string $destination, string $mode = 'driving'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockDirections();
        }

        try {
            $response = $this->getClient()->request('GET', 'https://maps.googleapis.com/maps/api/directions/json', [
                'query' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'mode' => $mode,
                    'key' => $this->apiKey
                ]
            ]);

            $data = $response->toArray();
            if ($data['status'] === 'OK' && !empty($data['routes'])) {
                return [
                    'success' => true,
                    'overview_polyline' => $data['routes'][0]['overview_polyline']['points'],
                    'steps' => $data['routes'][0]['legs'][0]['steps'],
                    'distance' => $data['routes'][0]['legs'][0]['distance']['text'],
                    'duration' => $data['routes'][0]['legs'][0]['duration']['text']
                ];
            }
            return ['success' => false, 'error' => $data['status'] ?? 'No routes found'];
        } catch (\Exception $e) {
            return $this->getMockDirections();
        }
    }

    public function getStaticMap(float $lat, float $lng, int $zoom = 14, int $width = 600, int $height = 400): string
    {
        if (empty($this->apiKey)) {
            return "https://via.placeholder.com/{$width}x{$height}?text=" . urlencode("Map: $lat, $lng");
        }

        return "https://maps.googleapis.com/maps/api/staticmap?center=$lat,$lng&zoom=$zoom&size={$width}x{$height}&markers=$lat,$lng&key={$this->apiKey}";
    }

    private function getMockCoordinates(string $address): array
    {
        $locations = [
            'paris' => ['lat' => 48.8566, 'lng' => 2.3522],
            'london' => ['lat' => 51.5074, 'lng' => -0.1278],
            'tokyo' => ['lat' => 35.6762, 'lng' => 139.6503],
            'new york' => ['lat' => 40.7128, 'lng' => -74.0060],
            'barcelona' => ['lat' => 41.3851, 'lng' => 2.1734]
        ];

        $key = strtolower(explode(',', $address)[0]);
        $coords = $locations[$key] ?? ['lat' => rand(40, 50), 'lng' => rand(-10, 30)];

        return [
            'success' => true,
            'lat' => $coords['lat'],
            'lng' => $coords['lng'],
            'formatted_address' => $address
        ];
    }

    private function getMockPlaceDetails(): array
    {
        return [
            'success' => true,
            'name' => 'Sample Place',
            'address' => '123 Sample Street, City, Country',
            'rating' => 4.5,
            'reviews' => [],
            'photos' => [],
            'opening_hours' => ['weekday_text' => ['Monday: 9:00 AM - 6:00 PM']],
            'website' => 'https://example.com',
            'phone' => '+1 234 567 8900'
        ];
    }

    private function getMockDirections(): array
    {
        return [
            'success' => true,
            'overview_polyline' => '',
            'steps' => [],
            'distance' => '10 km',
            'duration' => '15 min'
        ];
    }
}