<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SportsDataService
{
    private ?HttpClientInterface $client = null;

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchTeams(string $query): array
    {
        try {
            $response = $this->getClient()->request('GET', "https://www.thesportsdb.com/api/v1/json/3/searchteams.php", [
                'query' => ['t' => $query]
            ]);
            return $response->toArray();
        } catch (\Exception $e) {
            return ['teams' => []];
        }
    }

    public function getEvents(string $teamName): array
    {
        try {
            $response = $this->getClient()->request('GET', "https://www.thesportsdb.com/api/v1/json/3/searchevents.php", [
                'query' => ['e' => $teamName]
            ]);
            return $response->toArray();
        } catch (\Exception $e) {
            return ['events' => []];
        }
    }
}
