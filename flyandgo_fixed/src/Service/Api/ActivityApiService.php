<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ActivityApiService
{
    private ?HttpClientInterface $client = null;
    private PlacesApiService $placesService;
    private AiService $aiService;

    public function __construct(
        PlacesApiService $placesService,
        AiService $aiService
    ) {
        $this->placesService = $placesService;
        $this->aiService = $aiService;
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchActivities(string $query, string $location = ''): array
    {
        $searchQuery = $query . ($location ? " in $location" : "");
        $results = $this->placesService->searchText($searchQuery);
        
        return [
            'success' => true,
            'results' => $results['places'] ?? [],
            'query' => $query,
            'location' => $location
        ];
    }

    public function getPersonalizedRecommendations(string $location, array $userInterests = []): array
    {
        $interests = implode(', ', $userInterests);
        $aiResponse = $this->aiService->generateActivityRecommendations($location, $interests);
        
        return [
            'success' => true,
            'recommendations' => [],
            'ai_suggestions' => $aiResponse['response'] ?? '',
            'location' => $location
        ];
    }

    public function getActivityImages(string $activityName): array
    {
        try {
            $response = $this->getClient()->request('GET', 'https://api.unsplash.com/search/photos', [
                'query' => [
                    'query' => $activityName,
                    'per_page' => 4
                ]
            ]);

            $data = $response->toArray();
            $images = [];

            foreach ($data['results'] ?? [] as $photo) {
                $images[] = [
                    'regular' => $photo['urls']['regular'] ?? '',
                    'thumb' => $photo['urls']['thumb'] ?? '',
                    'alt' => $photo['alt_description'] ?? '',
                    'author' => $photo['user']['name'] ?? ''
                ];
            }

            return ['success' => true, 'images' => $images];
        } catch (\Exception $e) {
            return ['success' => false, 'images' => []];
        }
    }
}