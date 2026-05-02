<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocationService
{
    private ?HttpClientInterface $client = null;
    private string $ipInfoToken;

    public function __construct(string $ipInfoToken = '')
    {
        $this->ipInfoToken = $ipInfoToken ?: $_ENV['IPINFO_TOKEN'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getLocationFromIp(string $ip = ''): array
    {
        if (empty($this->ipInfoToken)) {
            return $this->getMockLocation();
        }

        try {
            $url = $ip ? "https://ipinfo.io/{$ip}/json" : 'https://ipinfo.io/json';
            $response = $this->getClient()->request('GET', $url, [
                'headers' => ['Authorization' => "Bearer {$this->ipInfoToken}"]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'ip' => $data['ip'] ?? $ip,
                'city' => $data['city'] ?? '',
                'region' => $data['region'] ?? '',
                'country' => $data['country'] ?? '',
                'location' => $data['loc'] ?? '',
                'timezone' => $data['timezone'] ?? '',
                'org' => $data['org'] ?? ''
            ];
        } catch (\Exception $e) {
            return $this->getMockLocation();
        }
    }

    public function geocodeAddress(string $address): array
    {
        try {
            $response = $this->getClient()->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1
                ],
                'headers' => ['User-Agent' => 'FlyAndGo/1.0']
            ]);

            $data = $response->toArray();
            if (!empty($data)) {
                return [
                    'success' => true,
                    'lat' => (float)$data[0]['lat'],
                    'lng' => (float)$data[0]['lon'],
                    'display_name' => $data[0]['display_name']
                ];
            }
            return ['success' => false, 'error' => 'No results found'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function reverseGeocode(float $lat, float $lng): array
    {
        try {
            $response = $this->getClient()->request('GET', 'https://nominatim.openstreetmap.org/reverse', [
                'query' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'format' => 'json'
                ],
                'headers' => ['User-Agent' => 'FlyAndGo/1.0']
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'address' => $data['display_name'] ?? '',
                'city' => $data['address']['city'] ?? $data['address']['town'] ?? '',
                'country' => $data['address']['country'] ?? ''
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getMockLocation(): array
    {
        return [
            'success' => true,
            'ip' => '192.168.1.1',
            'city' => 'Paris',
            'region' => 'Ile-de-France',
            'country' => 'FR',
            'location' => '48.8566,2.3522',
            'timezone' => 'Europe/Paris',
            'org' => 'Mock ISP'
        ];
    }
}