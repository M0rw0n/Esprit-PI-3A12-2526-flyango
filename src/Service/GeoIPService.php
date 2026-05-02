<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GeoIPService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private ?RequestStack $requestStack;

    public function __construct(
        ?string $geoapifyApiKey = null,
        ?HttpClientInterface $httpClient = null,
        ?RequestStack $requestStack = null
    ) {
        $this->apiKey = $geoapifyApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->requestStack = $requestStack;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_geoapify_api_key';
    }

    public function getVisitorInfo(): array
    {
        $ip = $this->getClientIP();
        
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return $this->getDefaultVisitorInfo();
        }

        return $this->getIPInfo($ip);
    }

    private function getClientIP(): ?string
    {
        if (!$this->requestStack) return null;
        
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) return null;

        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $header) {
            $ip = $request->server->get($header);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $request->getClientIp();
    }

    public function getIPInfo(string $ip): array
    {
        if ($this->isConfigured()) {
            try {
                $response = $this->httpClient->request(
                    'GET',
                    "https://ipapi.io/json/{$ip}?key={$this->apiKey}"
                );
                $data = $response->toArray();

                return [
                    'ip' => $ip,
                    'country' => $data['country_name'] ?? 'Unknown',
                    'country_code' => $data['country_code'] ?? '',
                    'region' => $data['region'] ?? '',
                    'city' => $data['city'] ?? 'Unknown',
                    'isp' => $data['connection']['isp'] ?? '',
                    'timezone' => $data['timezone'] ?? '',
                    'source' => 'api'
                ];
            } catch (\Exception $e) {
                // Fall back to mock data
            }
        }

        return $this->getMockIPInfo($ip);
    }

    private function getMockIPInfo(string $ip): array
    {
        $locations = [
            ['country' => 'Tunisie', 'country_code' => 'TN', 'region' => 'Tunis', 'city' => 'Tunis'],
            ['country' => 'France', 'country_code' => 'FR', 'region' => 'Île-de-France', 'city' => 'Paris'],
            ['country' => 'Algérie', 'country_code' => 'DZ', 'region' => 'Alger', 'city' => 'Alger'],
            ['country' => 'Maroc', 'country_code' => 'MA', 'region' => 'Casablanca', 'city' => 'Casablanca'],
            ['country' => 'Germany', 'country_code' => 'DE', 'region' => 'Berlin', 'city' => 'Berlin'],
            ['country' => 'United Kingdom', 'country_code' => 'GB', 'region' => 'London', 'city' => 'London'],
            ['country' => 'Italy', 'country_code' => 'IT', 'region' => 'Rome', 'city' => 'Rome'],
            ['country' => 'Spain', 'country_code' => 'ES', 'region' => 'Madrid', 'city' => 'Madrid'],
        ];

        $location = $locations[array_rand($locations)];

        return [
            'ip' => $ip,
            'country' => $location['country'],
            'country_code' => $location['country_code'],
            'region' => $location['region'],
            'city' => $location['city'],
            'isp' => 'Mock ISP',
            'timezone' => 'Africa/Tunis',
            'source' => 'mock'
        ];
    }

    private function getDefaultVisitorInfo(): array
    {
        return [
            'ip' => 'local',
            'country' => 'Tunisie',
            'country_code' => 'TN',
            'region' => 'Tunis',
            'city' => 'Tunis',
            'isp' => 'Local Network',
            'timezone' => 'Africa/Tunis',
            'source' => 'default'
        ];
    }

    public function getCountryStats(): array
    {
        return [
            ['country' => 'Tunisie', 'country_code' => 'TN', 'visitors' => rand(100, 200), 'percentage' => 35],
            ['country' => 'France', 'country_code' => 'FR', 'visitors' => rand(50, 100), 'percentage' => 20],
            ['country' => 'Algérie', 'country_code' => 'DZ', 'visitors' => rand(40, 80), 'percentage' => 15],
            ['country' => 'Maroc', 'country_code' => 'MA', 'visitors' => rand(30, 60), 'percentage' => 12],
            ['country' => 'Germany', 'country_code' => 'DE', 'visitors' => rand(20, 50), 'percentage' => 8],
            ['country' => 'Other', 'country_code' => 'XX', 'visitors' => rand(20, 40), 'percentage' => 10],
        ];
    }

    public function getTopCities(): array
    {
        return [
            ['city' => 'Tunis', 'country' => 'Tunisie', 'visitors' => rand(80, 150)],
            ['city' => 'Paris', 'country' => 'France', 'visitors' => rand(40, 80)],
            ['city' => 'Sfax', 'country' => 'Tunisie', 'visitors' => rand(30, 60)],
            ['city' => 'Sousse', 'country' => 'Tunisie', 'visitors' => rand(25, 50)],
            ['city' => 'Alger', 'country' => 'Algérie', 'visitors' => rand(20, 40)],
        ];
    }
}