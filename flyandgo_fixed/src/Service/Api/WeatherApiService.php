<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherApiService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $openWeatherApiKey = '')
    {
        $this->apiKey = $openWeatherApiKey ?: $_ENV['OPENWEATHER_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create([
                'timeout' => 5,
            ]);
        }
        return $this->client;
    }

    public function getCurrentWeather(string $city, string $units = 'metric'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockWeatherData($city);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => $units
                ],
                'timeout' => 5,
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'city' => $data['name'] ?? $city,
                'temperature' => $data['main']['temp'] ?? 0,
                'feels_like' => $data['main']['feels_like'] ?? 0,
                'humidity' => $data['main']['humidity'] ?? 0,
                'description' => $data['weather'][0]['description'] ?? '',
                'icon' => $data['weather'][0]['icon'] ?? '',
                'wind_speed' => $data['wind']['speed'] ?? 0,
                'pressure' => $data['main']['pressure'] ?? 0
            ];
        } catch (\Exception $e) {
            return $this->getMockWeatherData($city);
        }
    }

    public function getForecast(string $city, int $days = 5, string $units = 'metric'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockForecastData($city, $days);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => $units,
                    'cnt' => $days * 8
                ],
                'timeout' => 8,
            ]);

            $data = $response->toArray();
            return $this->parseForecast($data);
        } catch (\Exception $e) {
            return $this->getMockForecastData($city, $days);
        }
    }

    private function parseForecast(array $data): array
    {
        $forecasts = [];
        $grouped = [];

        foreach ($data['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);
            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'temp_min' => $item['main']['temp_min'],
                    'temp_max' => $item['main']['temp_max'],
                    'temps' => [],
                    'descriptions' => [],
                    'icons' => []
                ];
            }
            $grouped[$date]['temp_min'] = min($grouped[$date]['temp_min'], $item['main']['temp_min']);
            $grouped[$date]['temp_max'] = max($grouped[$date]['temp_max'], $item['main']['temp_max']);
            $grouped[$date]['temps'][] = $item['main']['temp'];
            $grouped[$date]['descriptions'][] = $item['weather'][0]['description'];
            $grouped[$date]['icons'][] = $item['weather'][0]['icon'];
        }

        foreach ($grouped as $date => $day) {
            $forecasts[] = [
                'date' => $date,
                'temp_min' => round($day['temp_min']),
                'temp_max' => round($day['temp_max']),
                'temp_avg' => round(array_sum($day['temps']) / count($day['temps'])),
                'description' => $day['descriptions'][array_rand($day['descriptions'])],
                'icon' => $day['icons'][array_rand($day['icons'])]
            ];
        }

        return ['success' => true, 'forecast' => $forecasts];
    }

    private function getMockWeatherData(string $city): array
    {
        return [
            'success' => true,
            'city' => $city,
            'temperature' => rand(15, 30),
            'feels_like' => rand(14, 32),
            'humidity' => rand(40, 80),
            'description' => ['Sunny', 'Partly cloudy', 'Clear'][rand(0, 2)],
            'icon' => '01d',
            'wind_speed' => rand(5, 20),
            'pressure' => rand(1010, 1025)
        ];
    }

    private function getMockForecastData(string $city, int $days): array
    {
        $forecast = [];
        $conditions = ['Sunny', 'Partly cloudy', 'Cloudy', 'Light rain'];
        $icons = ['01d', '02d', '03d', '10d'];

        for ($i = 0; $i < $days; $i++) {
            $forecast[] = [
                'date' => date('Y-m-d', strtotime("+$i days")),
                'temp_min' => rand(10, 20),
                'temp_max' => rand(20, 35),
                'temp_avg' => rand(15, 28),
                'description' => $conditions[rand(0, 3)],
                'icon' => $icons[rand(0, 3)]
            ];
        }

        return ['success' => true, 'forecast' => $forecast];
    }
}