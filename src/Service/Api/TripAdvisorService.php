<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TripAdvisorService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $tripAdvisorApiKey = '')
    {
        $this->apiKey = $tripAdvisorApiKey ?: $_ENV['TRIPADVISOR_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getReviews(string $locationId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockReviews($locationId);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.tripadvisor.com/api/partner/2.0/location/' . $locationId . '/reviews', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey
                ]
            ]);

            $data = $response->toArray();
            return $this->parseReviews($data['data'] ?? []);
        } catch (\Exception $e) {
            return $this->getMockReviews($locationId);
        }
    }

    public function searchLocations(string $query, string $lang = 'fr'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockLocations($query);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.tripadvisor.com/api/partner/2.0/search/' . $query, [
                'query' => [
                    'language' => $lang
                ]
            ]);

            $data = $response->toArray();
            return $this->parseLocations($data['data'] ?? []);
        } catch (\Exception $e) {
            return $this->getMockLocations($query);
        }
    }

    public function getLocationDetails(string $locationId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockLocationDetails($locationId);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.tripadvisor.com/api/partner/2.0/location/' . $locationId, []);
            $data = $response->toArray();

            return [
                'success' => true,
                'location' => [
                    'id' => $data['location_id'] ?? '',
                    'name' => $data['name'] ?? '',
                    'rating' => $data['rating'] ?? 0,
                    'num_reviews' => $data['num_reviews'] ?? 0,
                    'address' => $data['address'] ?? '',
                    'web_url' => $data['web_url'] ?? '',
                    'website' => $data['website'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'photo' => $data['photo'] ?? null
                ]
            ];
        } catch (\Exception $e) {
            return $this->getMockLocationDetails($locationId);
        }
    }

    private function parseReviews(array $reviews): array
    {
        $parsed = [];
        foreach ($reviews as $review) {
            $parsed[] = [
                'author' => $review['user'] ?? 'Anonyme',
                'rating' => $review['rating'] ?? 0,
                'date' => $review['published_date'] ?? '',
                'title' => $review['title'] ?? '',
                'text' => $review['text'] ?? ''
            ];
        }
        return $parsed;
    }

    private function parseLocations(array $locations): array
    {
        $parsed = [];
        foreach ($locations as $loc) {
            $parsed[] = [
                'id' => $loc['location_id'] ?? '',
                'name' => $loc['name'] ?? '',
                'rating' => $loc['rating'] ?? 0,
                'address' => $loc['address'] ?? ''
            ];
        }
        return $parsed;
    }

    private function getMockReviews(string $locationId): array
    {
        return [
            [
                'author' => 'Marie D.',
                'rating' => 5,
                'date' => date('Y-m-d', strtotime('-5 days')),
                'title' => 'Excellent expérience!',
                'text' => 'Une activité vraiment passionnante. Le guide était très professionnel et nous a fait découvrir des endroits incroyables. Je recommande fortement!'
            ],
            [
                'author' => 'Pierre L.',
                'rating' => 4,
                'date' => date('Y-m-d', strtotime('-10 days')),
                'title' => 'Très bonne activité',
                'text' => 'Bien organisé, ponctuel et instructif. Le seul bémol serait la durée un peu courte. Je recommande cette activité à tous les visiteurs.'
            ],
            [
                'author' => 'Sophie M.',
                'rating' => 5,
                'date' => date('Y-m-d', strtotime('-15 days')),
                'title' => 'Incontournable!',
                'text' => 'Le highlight de notre voyage à Djerba. À ne pas manquer! Le personnel était aux petits soins et les paysages sont magnifique.'
            ],
            [
                'author' => 'Ahmed B.',
                'rating' => 4,
                'date' => date('Y-m-d', strtotime('-20 days')),
                'title' => 'Belle découverte',
                'text' => 'Activité très intéressante avec des guides passionnés. Prévoir des chaussures de marche si vous avez prévu de marcher.'
            ],
            [
                'author' => 'Julie K.',
                'rating' => 5,
                'date' => date('Y-m-d', strtotime('-25 days')),
                'title' => 'Parfait pour les familles',
                'text' => 'Nos enfants ont adorés cette activité. Des souvenirs inoubliables! Merci à toute l\'équipe pour l\'accueil chaleureux.'
            ]
        ];
    }

    private function getMockLocations(string $query): array
    {
        return [
            [
                'id' => 'mock_1',
                'name' => "$query - Activité 1",
                'rating' => 4.5,
                'address' => 'Centre ville, Djerba'
            ],
            [
                'id' => 'mock_2',
                'name' => "$query - Activité 2",
                'rating' => 4.2,
                'address' => 'Zone touristique, Sousse'
            ]
        ];
    }

    private function getMockLocationDetails(string $locationId): array
    {
        return [
            'success' => true,
            'location' => [
                'id' => $locationId,
                'name' => 'Activité Mock',
                'rating' => 4.5,
                'num_reviews' => 128,
                'address' => '123 Rue principale, Djerba',
                'web_url' => 'https://tripadvisor.com',
                'website' => 'https://example.com',
                'phone' => '+216 12 345 678',
                'photo' => null
            ]
        ];
    }
}