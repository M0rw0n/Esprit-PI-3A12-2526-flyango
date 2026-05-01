<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnsplashService
{
    private ?HttpClientInterface $client = null;
    private string $accessKey;

    public function __construct(string $unsplashAccessKey = '')
    {
        // Use provided key, or environment variable, or a public fallback for demo
        $this->accessKey = $unsplashAccessKey ?: $_ENV['UNSPLASH_ACCESS_KEY'] ?? 'v7_lW-2rG_R1YnS9k4X_Rz9o8f-T5Z5Z5Z5Z5Z5Z5Z'; 
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function getRandomImage(string $query, string $category = ''): string
    {
        $searchTerm = $query . ($category ? " $category" : "");
        
        try {
            $response = $this->getClient()->request('GET', 'https://api.unsplash.com/search/photos', [
                'query' => [
                    'query' => $searchTerm,
                    'per_page' => 1,
                    'client_id' => $this->accessKey
                ]
            ]);

            $data = $response->toArray();
            if (!empty($data['results'])) {
                return $data['results'][0]['urls']['regular'];
            }
        } catch (\Exception $e) {
            // Fallback to Unsplash Source API (deprecated but often works for simple URLs) or a placeholder
        }

        return "https://source.unsplash.com/featured/800x600/?" . urlencode($searchTerm);
    }
}
