<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YelpService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $yelpApiKey = '')
    {
        $this->apiKey = $yelpApiKey ?: $_ENV['YELP_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create([
                'base_uri' => 'https://api.yelp.com/v3/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]);
        }
        return $this->client;
    }

    public function searchBusinesses(string $term, string $location, int $limit = 10): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockData($term, $location);
        }

        try {
            $response = $this->getClient()->request('GET', 'businesses/search', [
                'query' => [
                    'term' => $term,
                    'location' => $location,
                    'limit' => $limit,
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return $this->getMockData($term, $location);
        }
    }

    private function getMockData(string $term, string $location): array
    {
        $businesses = [];
        $names = [
            "Le Gourmet", "Dar El Jeld", "L'Astragale", "Chez Slah", "El Ali", 
            "La Villa", "The Grill", "Sushi Wan", "Pasta Cosi", "Le Pirate",
            "Café des Nattes", "Sidi Bou Said Tea", "M'Rabet", "Panorama", "Trocadero"
        ];
        
        $categories = ['Restaurant', 'Café', 'Bar', 'Bistro', 'Fast Food'];

        for ($i = 0; $i < 20; $i++) {
            $name = $names[$i % count($names)] . " " . ($i > count($names) ? $i : "");
            $businesses[] = [
                'name' => "$name " . ucfirst($location),
                'rating' => 3.5 + (rand(0, 15) / 10),
                'price' => str_repeat('$', rand(1, 3)),
                'display_phone' => '+216 ' . rand(70, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'categories' => [['title' => $categories[rand(0, count($categories)-1)]]],
                'image_url' => 'https://images.unsplash.com/photo-' . (1517248135467 + $i) . '-4c7edcad34c4?w=500',
                'location' => ['address1' => rand(1, 100) . ' Avenue Habib Bourguiba']
            ];
        }

        return ['businesses' => $businesses];
    }
}
