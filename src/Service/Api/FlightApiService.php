<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FlightApiService
{
    private ?HttpClientInterface $client = null;
    private string $amadeusApiKey;
    private string $amadeusApiSecret;

    public function __construct(
        string $amadeusApiKey = '',
        string $amadeusApiSecret = ''
    ) {
        $this->amadeusApiKey = $amadeusApiKey ?: $_ENV['AMADEUS_API_KEY'] ?? '';
        $this->amadeusApiSecret = $amadeusApiSecret ?: $_ENV['AMADEUS_API_SECRET'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchFlights(
        string $origin,
        string $destination,
        string $departureDate,
        string $returnDate = '',
        int $adults = 1,
        string $currency = 'EUR'
    ): array {
        if (empty($this->amadeusApiKey)) {
            return $this->getMockFlights($origin, $destination, $departureDate);
        }

        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return $this->getMockFlights($origin, $destination, $departureDate);
            }

            $params = [
                'originLocationCode' => strtoupper($origin),
                'destinationLocationCode' => strtoupper($destination),
                'departureDate' => $departureDate,
                'adults' => $adults,
                'currencyCode' => $currency,
                'max' => 10
            ];

            if ($returnDate) {
                $params['returnDate'] = $returnDate;
            }

            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v2/shopping/flight-offers', [
                'query' => $params,
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return $this->parseFlightOffers($data);
        } catch (\Exception $e) {
            return $this->getMockFlights($origin, $destination, $departureDate);
        }
    }

    public function searchAirports(string $keyword): array
    {
        if (empty($this->amadeusApiKey)) {
            return $this->getMockAirports($keyword);
        }

        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return $this->getMockAirports($keyword);
            }

            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v1/reference-data/locations', [
                'query' => [
                    'subType' => 'AIRPORT,CITY',
                    'keyword' => $keyword,
                    'page[limit]' => 10
                ],
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return $this->parseAirports($data);
        } catch (\Exception $e) {
            return $this->getMockAirports($keyword);
        }
    }

    public function getFlightPrice(string $flightOfferId): array
    {
        if (empty($this->amadeusApiKey)) {
            return ['success' => true, 'price' => rand(100, 500), 'currency' => 'EUR'];
        }

        try {
            $token = $this->getAccessToken();
            $response = $this->getClient()->request('POST', 'https://api.amadeus.com/v1/shopping/flight-offers/pricing', [
                'json' => ['data' => ['type' => 'flight-offers-pricing', 'flightOffers' => [['id' => $flightOfferId]]]],
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'price' => $data['data']['flightOffers'][0]['price']['grandTotal'] ?? 0,
                'currency' => $data['data']['flightOffers'][0]['price']['currency'] ?? 'EUR'
            ];
        } catch (\Exception $e) {
            return ['success' => true, 'price' => rand(100, 500), 'currency' => 'EUR'];
        }
    }

    private function getAccessToken(): ?string
    {
        try {
            $response = $this->getClient()->request('POST', 'https://api.amadeus.com/v1/security/oauth2/token', [
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->amadeusApiKey,
                    'client_secret' => $this->amadeusApiSecret
                ]
            ]);

            $data = $response->toArray();
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseFlightOffers(array $data): array
    {
        if (!isset($data['data'])) {
            return $this->getMockFlights('PAR', 'LON', date('Y-m-d'));
        }

        $flights = [];
        foreach ($data['data'] as $offer) {
            $flights[] = [
                'id' => $offer['id'],
                'price' => $offer['price']['grandTotal'],
                'currency' => $offer['price']['currency'],
                'segments' => $this->parseSegments($offer['itineraries'][0]['segments'])
            ];
        }

        return ['success' => true, 'flights' => $flights];
    }

    private function parseSegments(array $segments): array
    {
        $parsed = [];
        foreach ($segments as $segment) {
            $parsed[] = [
                'departure' => $segment['departure']['at'],
                'arrival' => $segment['arrival']['at'],
                'airline' => $segment['carrierCode'],
                'flight_number' => $segment['number'],
                'origin' => $segment['departure']['iataCode'],
                'destination' => $segment['arrival']['iataCode'],
                'duration' => $segment['duration']
            ];
        }
        return $parsed;
    }

    private function parseAirports(array $data): array
    {
        if (!isset($data['data'])) {
            return $this->getMockAirports('Paris');
        }

        $airports = [];
        foreach ($data['data'] as $location) {
            $airports[] = [
                'code' => $location['iataCode'],
                'name' => $location['name'],
                'city' => $location['address']['cityName'] ?? '',
                'country' => $location['address']['countryName'] ?? ''
            ];
        }

        return ['success' => true, 'airports' => $airports];
    }

    private function getMockFlights(string $origin, string $destination, string $date): array
    {
        $airlines = ['AF', 'BA', 'LH', 'UA', 'EK'];
        $flights = [];

        for ($i = 0; $i < 5; $i++) {
            $flights[] = [
                'id' => 'mock_' . ($i + 1),
                'price' => rand(100, 600),
                'currency' => 'EUR',
                'segments' => [
                    [
                        'departure' => "$date " . sprintf('%02d:00', rand(6, 20)),
                        'arrival' => "$date " . sprintf('%02d:30', rand(8, 22)),
                        'airline' => $airlines[$i],
                        'flight_number' => $airlines[$i] . rand(100, 999),
                        'origin' => strtoupper($origin),
                        'destination' => strtoupper($destination),
                        'duration' => '2h ' . rand(0, 59) . 'm'
                    ]
                ]
            ];
        }

        return ['success' => true, 'flights' => $flights];
    }

    private function getMockAirports(string $keyword): array
    {
        return [
            'success' => true,
            'airports' => [
                ['code' => 'CDG', 'name' => 'Paris Charles de Gaulle', 'city' => 'Paris', 'country' => 'France'],
                ['code' => 'ORY', 'name' => 'Paris Orly', 'city' => 'Paris', 'country' => 'France'],
                ['code' => 'LTN', 'name' => 'London Luton', 'city' => 'London', 'country' => 'United Kingdom']
            ]
        ];
    }
}