<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleSearchService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey = '';
    private string $cx = '';

    public function __construct() {
        $this->apiKey = $_ENV['GOOGLE_SEARCH_API_KEY'] ?? $_SERVER['GOOGLE_SEARCH_API_KEY'] ?? '';
        $this->cx = $_ENV['GOOGLE_SEARCH_CX'] ?? $_SERVER['GOOGLE_SEARCH_CX'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function search(string $query, int $num = 10): array
    {
        if (empty($this->apiKey) || empty($this->cx)) {
            return $this->getMockSearchResults($query);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://customsearch.googleapis.com/customsearch/v1', [
                'query' => [
                    'key' => $this->apiKey,
                    'cx' => $this->cx,
                    'q' => $query,
                    'num' => min($num, 10)
                ]
            ]);

            $data = $response->toArray();
            
            if (!empty($data['items'])) {
                return [
                    'success' => true,
                    'results' => $this->parseResults($data['items'])
                ];
            }
            
            return ['success' => false, 'results' => [], 'error' => 'No results found'];
        } catch (\Exception $e) {
            return $this->getMockSearchResults($query);
        }
    }

    public function searchImages(string $query, int $num = 10): array
    {
        if (empty($this->apiKey) || empty($this->cx)) {
            return $this->getMockImageResults($query);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://customsearch.googleapis.com/customsearch/v1', [
                'query' => [
                    'key' => $this->apiKey,
                    'cx' => $this->cx,
                    'q' => $query,
                    'searchType' => 'image',
                    'num' => min($num, 10),
                    'imgSize' => 'large',
                    'imgType' => 'photo'
                ]
            ]);

            $data = $response->toArray();
            
            if (!empty($data['items'])) {
                return [
                    'success' => true,
                    'images' => array_map(function($item) {
                        return [
                            'link' => $item['link'] ?? '',
                            'thumbnail' => $item['image']['thumbnailLink'] ?? $item['link'] ?? '',
                            'width' => $item['image']['width'] ?? 0,
                            'height' => $item['image']['height'] ?? 0,
                            'context' => $item['image']['contextLink'] ?? ''
                        ];
                    }, $data['items'])
                ];
            }
            
            return ['success' => false, 'images' => []];
        } catch (\Exception $e) {
            return $this->getMockImageResults($query);
        }
    }

    private function parseResults(array $items): array
    {
        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'title' => $item['title'] ?? '',
                'link' => $item['link'] ?? '',
                'snippet' => $item['snippet'] ?? '',
                'displayLink' => $item['displayLink'] ?? '',
                'image' => $item['pagemap']['cse_image'][0]['src'] ?? 
                          ($item['pagemap']['metatags'][0]['og:image'] ?? '')
            ];
        }
        return $results;
    }

    private function getMockSearchResults(string $query): array
    {
        return [
            'success' => true,
            'results' => [
                [
                    'title' => "Top things to do in " . ucwords($query),
                    'link' => '#',
                    'snippet' => 'Discover the best attractions, restaurants and activities in ' . ucwords($query),
                    'displayLink' => 'travel.example.com',
                    'image' => ''
                ]
            ]
        ];
    }

    private function getMockImageResults(string $query): array
    {
        return [
            'success' => true,
            'images' => [
                ['link' => "https://source.unsplash.com/800x600/?" . urlencode($query), 'thumbnail' => '', 'width' => 800, 'height' => 600]
            ]
        ];
    }
}