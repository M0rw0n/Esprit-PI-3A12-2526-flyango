<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WikipediaService
{
    private ?HttpClientInterface $client = null;

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchPlaces(string $query, string $lang = 'fr', int $limit = 10): array
    {
        try {
            $response = $this->getClient()->request('GET', "https://$lang.wikipedia.org/w/api.php", [
                'query' => [
                    'action' => 'query',
                    'list' => 'search',
                    'srsearch' => $query . ' Tunisia tourisme lieu',
                    'srlimit' => $limit,
                    'format' => 'json',
                    'utf8' => 1
                ]
            ]);

            $data = $response->toArray();
            $results = $data['query']['search'] ?? [];

            $places = [];
            foreach ($results as $item) {
                $imageUrl = $this->getPageImage($item['pageid'], $lang);
                $places[] = [
                    'id' => $item['pageid'],
                    'title' => str_replace([' (Tunisie)', ' (Tunisia)'], '', $item['title']),
                    'snippet' => strip_tags($item['snippet']),
                    'pageid' => $item['pageid'],
                    'image_url' => $imageUrl ?: $this->generatePlaceholderImage($item['title'])
                ];
            }

            return $places;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function searchRestaurants(string $location, string $lang = 'fr', int $limit = 12): array
    {
        return $this->searchPlaces("restaurants $location", $lang, $limit);
    }

    public function searchAttractions(string $location, string $lang = 'fr', int $limit = 12): array
    {
        return $this->searchPlaces("à voir $location tourisme", $lang, $limit);
    }

    public function searchHotels(string $location, string $lang = 'fr', int $limit = 8): array
    {
        return $this->searchPlaces("hôtel $location", $lang, $limit);
    }

    private function getPageImage(int $pageId, string $lang = 'fr'): ?string
    {
        try {
            $response = $this->getClient()->request('GET', "https://$lang.wikipedia.org/w/api.php", [
                'query' => [
                    'action' => 'query',
                    'pageids' => $pageId,
                    'prop' => 'pageimages',
                    'pithumbsize' => 500,
                    'format' => 'json'
                ]
            ]);

            $data = $response->toArray();
            $page = $data['query']['pages'][$pageId] ?? [];
            
            return $page['thumbnail']['source'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generatePlaceholderImage(string $title): string
    {
        $keywords = urlencode($title . ' Tunisia landmark');
        return "https://image.pollinations.ai/prompt/$keywords?width=800&height=600&nologo=true";
    }

    public function getCityInfo(string $cityName, string $lang = 'fr'): array
    {
        try {
            $response = $this->getClient()->request('GET', "https://$lang.wikipedia.org/w/api.php", [
                'query' => [
                    'action' => 'query',
                    'titles' => $cityName,
                    'prop' => 'extracts|pageimages|info',
                    'exintro' => true,
                    'explaintext' => true,
                    'pithumbsize' => 400,
                    'inprop' => 'url',
                    'format' => 'json'
                ]
            ]);

            $data = $response->toArray();
            $pages = $data['query']['pages'] ?? [];
            $page = reset($pages);

            if ($page && isset($page['pageid'])) {
                return [
                    'name' => $page['title'] ?? $cityName,
                    'description' => $page['extract'] ?? '',
                    'image_url' => $page['thumbnail']['source'] ?? $this->generatePlaceholderImage($cityName),
                    'url' => $page['fullurl'] ?? ''
                ];
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}