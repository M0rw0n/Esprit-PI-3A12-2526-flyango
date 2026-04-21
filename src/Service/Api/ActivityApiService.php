<?php

namespace App\Service\Api;

use App\Service\NearbyService;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ActivityApiService
{
    private ?HttpClientInterface $client = null;
    private PlacesApiService $placesService;
    private AiService $aiService;
    private ?NearbyService $nearbyService = null;

    public function __construct(
        PlacesApiService $placesService,
        AiService $aiService,
        ?NearbyService $nearbyService = null
    ) {
        $this->placesService = $placesService;
        $this->aiService = $aiService;
        $this->nearbyService = $nearbyService;
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getNearbyActivities(float $lat, float $lng, string $category = '', int $radius = 5000): array
    {
        // Use real database activities if NearbyService is available
        if ($this->nearbyService) {
            $radiusKm = $radius / 1000; // Convert meters to km
            $results = $this->nearbyService->getNearbyActivities($lat, $lng, $radiusKm, 10);
            
            $activities = [];
            foreach ($results as $result) {
                $activity = $result['activity'];
                $activities[] = [
                    'id' => $activity->getId(),
                    'name' => $activity->getTitle(),
                    'address' => $activity->getLieu(),
                    'lat' => null,
                    'lng' => null,
                    'rating' => $activity->getNoteMoyenne(),
                    'type' => $activity->getCategory() ?: 'activity',
                    'price' => $activity->getPrice(),
                    'distance' => $result['distance'] . ' km'
                ];
            }
            
            return [
                'success' => true,
                'activities' => $activities,
                'total' => count($activities)
            ];
        }
        
        // Fallback to Places API (mock data)
        $type = $this->mapCategoryToPlaceType($category);
        $places = $this->placesService->searchNearbyPlaces($lat, $lng, $type, $radius);
        
        $activities = [];
        foreach ($places['places'] ?? [] as $place) {
            $activities[] = [
                'id' => $place['id'],
                'name' => $place['name'],
                'address' => $place['address'],
                'lat' => $place['lat'],
                'lng' => $place['lng'],
                'rating' => $place['rating'],
                'type' => $category ?: 'activity'
            ];
        }
        
        return [
            'success' => true,
            'activities' => $activities,
            'total' => count($activities)
        ];
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
        
        $coords = (new MapsApiService())->getCoordinates($location);
        $lat = $coords['lat'] ?? 0;
        $lng = $coords['lng'] ?? 0;
        
        $categories = $userInterests ?: ['restaurant', 'museum', 'park'];
        $activities = [];
        
        foreach ($categories as $category) {
            $places = $this->getNearbyActivities($lat, $lng, $category, 3000);
            $activities = array_merge($activities, $places['activities'] ?? []);
        }
        
        return [
            'success' => true,
            'recommendations' => $activities,
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

    private function mapCategoryToPlaceType(string $category): string
    {
        $mapping = [
            'restaurant' => 'restaurant',
            'culture' => 'museum',
            'nature' => 'park',
            'shopping' => 'shopping_mall',
            'sport' => 'gym',
            'loisir' => 'amusement_park',
            'spa' => 'spa',
            'plage' => 'beach',
            'default' => 'point_of_interest'
        ];

        return $mapping[mb_strtolower($category)] ?? $mapping['default'];
    }
}