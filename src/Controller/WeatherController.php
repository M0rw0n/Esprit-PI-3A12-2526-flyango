<?php

namespace App\Controller;

use App\Service\Api\WeatherApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    public function __construct(
        private readonly WeatherApiService $weatherService,
    ) {}

    #[Route('/api/weather', name: 'api_weather', methods: ['GET'])]
    public function getWeather(Request $request): JsonResponse
    {
        $city = $request->query->get('city');
        
        if (!$city) {
            return $this->json(['success' => false, 'error' => 'City required']);
        }

        $weather = $this->weatherService->getCurrentWeather($city);
        
        if ($weather) {
            return $this->json(['success' => true, 'weather' => $weather]);
        }
        
        return $this->json(['success' => false, 'error' => 'Weather not found']);
    }
}