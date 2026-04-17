<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(?string $openWeatherApiKey = null, ?HttpClientInterface $httpClient = null)
    {
        $this->apiKey = $openWeatherApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_openweather_api_key';
    }

    public function getCurrentWeather(string $city): ?array
    {
        if (!$this->isConfigured()) {
            return $this->getMockWeather($city);
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$this->apiKey}&units=metric&lang=fr"
            );
            $data = $response->toArray();

            return [
                'city' => $data['name'] ?? $city,
                'temperature' => $data['main']['temp'] ?? 0,
                'feels_like' => $data['main']['feels_like'] ?? 0,
                'humidity' => $data['main']['humidity'] ?? 0,
                'description' => $data['weather'][0]['description'] ?? '',
                'icon' => $data['weather'][0]['icon'] ?? '01d',
                'wind_speed' => $data['wind']['speed'] ?? 0,
                'source' => 'api'
            ];
        } catch (\Exception $e) {
            return $this->getMockWeather($city);
        }
    }

    public function getForecast(string $city, int $days = 5): ?array
    {
        if (!$this->isConfigured()) {
            return $this->getMockForecast($city, $days);
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$this->apiKey}&units=metric&lang=fr&cnt=" . ($days * 8)
            );
            $data = $response->toArray();

            $forecasts = [];
            foreach ($data['list'] as $item) {
                $forecasts[] = [
                    'date' => $item['dt_txt'],
                    'temperature' => $item['main']['temp'],
                    'description' => $item['weather'][0]['description'] ?? '',
                    'icon' => $item['weather'][0]['icon'] ?? '01d',
                ];
            }

            return [
                'city' => $data['city']['name'] ?? $city,
                'forecasts' => $forecasts,
                'source' => 'api'
            ];
        } catch (\Exception $e) {
            return $this->getMockForecast($city, $days);
        }
    }

    public function getWeatherForDestinations(array $destinations): array
    {
        $results = [];
        foreach ($destinations as $destination) {
            $results[$destination] = $this->getCurrentWeather($destination);
        }
        return $results;
    }

    private function getMockWeather(string $city): array
    {
        $conditions = [
            'Djerba' => ['temp' => 22, 'desc' => 'Ensoleillé', 'icon' => '01d'],
            'Tunis' => ['temp' => 18, 'desc' => 'Partiellement nuageux', 'icon' => '02d'],
            'Sfax' => ['temp' => 20, 'desc' => 'Ciel dégagé', 'icon' => '01d'],
            'Sousse' => ['temp' => 19, 'desc' => 'Nuageux', 'icon' => '03d'],
            'Carthage' => ['temp' => 18, 'desc' => 'Vent légère', 'icon' => '02d'],
        ];

        $condition = $conditions[$city] ?? ['temp' => 20, 'desc' => 'Donné météo', 'icon' => '01d'];

        return [
            'city' => $city,
            'temperature' => $condition['temp'],
            'feels_like' => $condition['temp'] - 2,
            'humidity' => rand(50, 70),
            'description' => $condition['desc'],
            'icon' => $condition['icon'],
            'wind_speed' => rand(5, 15),
            'source' => 'mock'
        ];
    }

    private function getMockForecast(string $city, int $days): array
    {
        $forecasts = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $forecasts[] = [
                'date' => $date,
                'temperature' => rand(16, 26),
                'description' => ['Ensoleillé', 'Nuageux', 'Partiellement nuageux'][rand(0, 2)],
                'icon' => ['01d', '02d', '03d'][rand(0, 2)],
            ];
        }

        return [
            'city' => $city,
            'forecasts' => $forecasts,
            'source' => 'mock'
        ];
    }

    public function getWeatherAlert(string $city): ?array
    {
        $weather = $this->getCurrentWeather($city);
        
        if (!$weather) return null;

        $alerts = [];

        if ($weather['temperature'] > 35) {
            $alerts[] = ['type' => 'heat', 'level' => 'warning', 'message' => 'Canicule prévue - Advice pour hydrated'];
        } elseif ($weather['temperature'] < 5) {
            $alerts[] = ['type' => 'cold', 'level' => 'warning', 'message' => 'Froid intense - Advice pour vêtements chaud'];
        }

        if ($weather['wind_speed'] > 50) {
            $alerts[] = ['type' => 'wind', 'level' => 'danger', 'message' => 'Vent fort - Advice d\'éviter activités outdoors'];
        }

        if ($weather['humidity'] > 85) {
            $alerts[] = ['type' => 'humidity', 'level' => 'info', 'message' => 'Humidité élevée'];
        }

        return !empty($alerts) ? ['city' => $city, 'alerts' => $alerts] : null;
    }
}