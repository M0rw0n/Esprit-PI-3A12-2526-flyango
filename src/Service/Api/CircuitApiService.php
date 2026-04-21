<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CircuitApiService
{
    private ?HttpClientInterface $client = null;
    private WeatherApiService $weatherService;
    private MapsApiService $mapsService;
    private AiService $aiService;

    public function __construct(
        WeatherApiService $weatherService,
        MapsApiService $mapsService,
        AiService $aiService
    ) {
        $this->weatherService = $weatherService;
        $this->mapsService = $mapsService;
        $this->aiService = $aiService;
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getDestinationInfo(string $destination): array
    {
        $weather = $this->weatherService->getCurrentWeather($destination);
        $coords = $this->mapsService->getCoordinates($destination);
        
        return [
            'success' => true,
            'weather' => $weather,
            'coordinates' => $coords,
            'best_time' => $this->getBestTravelTime($destination)
        ];
    }

    public function generateSmartItinerary(string $destination, array $preferences = []): array
    {
        $aiResponse = $this->aiService->generateCircuitSuggestions($destination, $preferences);
        
        $weatherForecast = $this->weatherService->getForecast($destination, 7);
        
        $coords = $this->mapsService->getCoordinates($destination);
        
        return [
            'success' => true,
            'itinerary' => $aiResponse['response'] ?? '',
            'weather_forecast' => $weatherForecast['forecast'] ?? [],
            'coordinates' => $coords,
            'generated' => date('Y-m-d H:i:s')
        ];
    }

    public function getWeatherRecommendations(string $destination, string $departureDate): array
    {
        $currentWeather = $this->weatherService->getCurrentWeather($destination);
        $forecast = $this->weatherService->getForecast($destination, 14);
        
        $departureTimestamp = strtotime($departureDate);
        $recommended = [];
        
        foreach ($forecast['forecast'] ?? [] as $day) {
            $dayTimestamp = strtotime($day['date']);
            if ($dayTimestamp >= $departureTimestamp) {
                $temp = $day['temp_avg'];
                $condition = mb_strtolower($day['description']);
                
                if ($temp >= 15 && $temp <= 30 && 
                    !str_contains($condition, 'rain') && 
                    !str_contains($condition, 'storm')) {
                    $recommended[] = $day;
                }
            }
        }
        
        return [
            'success' => true,
            'current_weather' => $currentWeather,
            'forecast' => $forecast['forecast'] ?? [],
            'recommended_dates' => $recommended,
            'recommendation' => count($recommended) > 0 ? 
                'Bon moment pour voyager' : 
                'Envisagez des dates alternatives'
        ];
    }

    private function getBestTravelTime(string $destination): array
    {
        return [
            'best_months' => ['Avril', 'Mai', 'Juin', 'Septembre', 'Octobre'],
            'peak_season' => 'Juillet - Août',
            'off_peak' => 'Novembre - Mars',
            'avg_temp_summer' => 28,
            'avg_temp_winter' => 12
        ];
    }

    public function getNearbyAttractions(float $lat, float $lng, string $type = ''): array
    {
        $response = $this->getClient()->request('GET', 'https://api.unsplash.com/search/photos', [
            'query' => [
                'query' => $type ?: 'travel',
                'per_page' => 6
            ]
        ]);

        $data = $response->toArray();
        
        $images = [];
        foreach ($data['results'] ?? [] as $photo) {
            $images[] = [
                'url' => $photo['urls']['regular'] ?? '',
                'thumb' => $photo['urls']['thumb'] ?? '',
                'credit' => $photo['user']['name'] ?? ''
            ];
        }

        return [
            'success' => true,
            'images' => $images
        ];
    }
}