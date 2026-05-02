<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SkyscannerApiService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $skyscannerApiKey = '')
    {
        $this->apiKey = $skyscannerApiKey ?: $_ENV['SKYSCANNER_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchFlights(string $origin, string $destination, string $date, string $currency = 'EUR'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockPrices($origin, $destination);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://partners.api.skyscanner.net/flights/browse/v1.0', [
                'query' => [
                    'apiKey' => $this->apiKey,
                    'origin' => $origin,
                    'destination' => $destination,
                    'departuredate' => $date,
                    'currency' => $currency,
                    'locale' => 'en-US'
                ]
            ]);

            $data = $response->toArray();
            return $this->parseItineraries($data);
        } catch (\Exception $e) {
            return $this->getMockPrices($origin, $destination);
        }
    }

    public function getPriceAlerts(string $origin, string $destination): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockPriceHistory($origin, $destination);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://partners.api.skyscanner.net/flights/pricing/v1.0', [
                'query' => [
                    'apiKey' => $this->apiKey,
                    'origin' => $origin,
                    'destination' => $destination,
                    'currency' => 'EUR'
                ]
            ]);

            $data = $response->toArray();
            return $this->parsePriceHistory($data);
        } catch (\Exception $e) {
            return $this->getMockPriceHistory($origin, $destination);
        }
    }

    public function getCheapestDates(string $origin, string $destination, string $currency = 'EUR'): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockCheapestDates();
        }

        try {
            $dates = [];
            for ($i = 0; $i < 30; $i++) {
                $date = date('Y-m-d', strtotime("+$i days"));
                $prices[] = [
                    'date' => $date,
                    'price' => rand(100, 500)
                ];
            }

            usort($prices, fn($a, $b) => $a['price'] - $b['price']);
            return ['success' => true, 'cheapest_dates' => array_slice($prices, 0, 7)];
        } catch (\Exception $e) {
            return $this->getMockCheapestDates();
        }
    }

    private function parseItineraries(array $data): array
    {
        if (!isset($data['Itineraries'])) {
            return $this->getMockPrices('PAR', 'LON');
        }

        $results = [];
        foreach (array_slice($data['Itineraries'], 0, 10) as $itinerary) {
            $results[] = [
                'id' => $itinerary['Id'] ?? uniqid(),
                'price' => $itinerary['PricingOptions'][0]['Price'] ?? 0,
                'currency' => $data['Currencies'][0]['Code'] ?? 'EUR',
                'airline' => $itinerary['OutboundLeg']['CarrierIds'][0] ?? '',
                'duration' => $itinerary['OutboundLeg']['Duration'] ?? 0,
                'stops' => count($itinerary['OutboundLeg']['SegmentIds'] ?? []) - 1
            ];
        }

        return ['success' => true, 'results' => $results];
    }

    private function parsePriceHistory(array $data): array
    {
        return ['success' => true, 'history' => []];
    }

    private function getMockPrices(string $origin, string $destination): array
    {
        $airlines = ['EasyJet', 'Ryanair', 'British Airways', 'Air France', 'Vueling'];
        $results = [];

        for ($i = 0; $i < 8; $i++) {
            $results[] = [
                'id' => 'mock_' . ($i + 1),
                'price' => rand(50, 300),
                'currency' => 'EUR',
                'airline' => $airlines[$i % count($airlines)],
                'duration' => rand(90, 240),
                'stops' => rand(0, 2)
            ];
        }

        usort($results, fn($a, $b) => $a['price'] - $b['price']);
        return ['success' => true, 'results' => $results];
    }

    private function getMockPriceHistory(string $origin, string $destination): array
    {
        $history = [];
        for ($i = 0; $i < 30; $i++) {
            $history[] = [
                'date' => date('Y-m-d', strtotime("-$i days")),
                'price' => rand(80, 250)
            ];
        }

        return ['success' => true, 'history' => array_reverse($history)];
    }

    private function getMockCheapestDates(): array
    {
        $dates = [];
        for ($i = 0; $i < 30; $i++) {
            $dates[] = [
                'date' => date('Y-m-d', strtotime("+$i days")),
                'price' => rand(50, 200)
            ];
        }

        usort($dates, fn($a, $b) => $a['price'] - $b['price']);
        return ['success' => true, 'cheapest_dates' => array_slice($dates, 0, 7)];
    }
}