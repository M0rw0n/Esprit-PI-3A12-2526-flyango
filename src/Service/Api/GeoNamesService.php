<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoNamesService
{
    private ?HttpClientInterface $client = null;
    private string $username;

    public function __construct(string $geoNamesUsername = '')
    {
        $this->username = $geoNamesUsername ?: $_ENV['GEONAMES_USERNAME'] ?? 'demo';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchCity(string $query): array
    {
        try {
            $response = $this->getClient()->request('GET', "http://api.geonames.org/searchJSON", [
                'query' => [
                    'q' => $query,
                    'maxRows' => 5,
                    'username' => $this->username
                ]
            ]);
            return $response->toArray();
        } catch (\Exception $e) {
            return ['geonames' => []];
        }
    }

    public function getCityInfo(int $geonameId): array
    {
        try {
            $response = $this->getClient()->request('GET', "http://api.geonames.org/getJSON", [
                'query' => [
                    'geonameId' => $geonameId,
                    'username' => $this->username
                ]
            ]);
            return $response->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
