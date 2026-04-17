<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AmadeusHotelService
{
    private ?HttpClientInterface $client = null;
    private string $amadeusApiKey;
    private string $amadeusApiSecret;
    private string $accessToken;
    private int $tokenExpiry = 0;

    private array $cityCodeMap = [
        'tunis' => 'TUN',
        'paris' => 'PAR',
        'london' => 'LON',
        'madrid' => 'MAD',
        'barcelona' => 'BCN',
        'rome' => 'ROM',
        'milan' => 'MIL',
        'berlin' => 'BER',
        'dubai' => 'DXB',
        ' Marrakech' => 'RAK',
        'djerba' => 'DJE',
        'sfax' => 'SFA',
        'hammamet' => 'SXM',
        'carthage' => 'Carthage',
        'sousse' => 'SUE',
        'monastir' => 'MIR',
        'Tozeur' => 'TOZ',
        'cabana' => 'CVA',
    ];

    public function __construct(
        string $amadeusApiKey = '',
        string $amadeusApiSecret = ''
    ) {
        $this->amadeusApiKey = $amadeusApiKey ?: $_ENV['AMADEUS_API_KEY'] ?? getenv('AMADEUS_API_KEY') ?: '';
        $this->amadeusApiSecret = $amadeusApiSecret ?: $_ENV['AMADEUS_API_SECRET'] ?? getenv('AMADEUS_API_SECRET') ?: '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    private function getAccessToken(): ?string
    {
        if (!empty($this->accessToken) && time() < $this->tokenExpiry) {
            return $this->accessToken;
        }

        if (empty($this->amadeusApiKey) || empty($this->amadeusApiSecret)) {
            return null;
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api.amadeus.com/v1/security/oauth2/token', [
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->amadeusApiKey,
                    'client_secret' => $this->amadeusApiSecret
                ],
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
            ]);

            $data = $response->toArray();
            $this->accessToken = $data['access_token'] ?? null;
            $this->tokenExpiry = time() + ($data['expires_in'] ?? 1800);

            return $this->accessToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchHotelsByCity(string $cityCode, int $radius = 50): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return $this->getMockHotels($cityCode);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v1/reference-data/locations/hotels/by-city', [
                'query' => [
                    'cityCode' => strtoupper($cityCode),
                    'radius' => $radius,
                    'hotelSource' => 'ALL'
                ],
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return $this->parseHotels($data);
        } catch (\Exception $e) {
            return $this->getMockHotels($cityCode);
        }
    }

    public function searchHotelsByCoordinates(float $latitude, float $longitude, int $radius = 50): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return $this->getMockHotels('LAT:' . $latitude);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v1/reference-data/locations/hotels/by-geocode', [
                'query' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius' => $radius,
                    'hotelSource' => 'ALL'
                ],
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return $this->parseHotels($data);
        } catch (\Exception $e) {
            return $this->getMockHotels('geo');
        }
    }

    public function getHotelDetails(string $hotelId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'No API credentials'];
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v1/reference-data/hotels/' . $hotelId, [
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return ['success' => true, 'hotel' => $this->parseHotelDetails($data)];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getHotelOffers(string $hotelId, array $checkInOut, int $adults = 1): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'No API credentials'];
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.amadeus.com/v2/shopping/hotel-offers', [
                'query' => [
                    'hotelId' => $hotelId,
                    'checkInDate' => $checkInOut['checkIn'] ?? date('Y-m-d'),
                    'checkOutDate' => $checkInOut['checkOut'] ?? date('Y-m-d', strtotime('+1 day')),
                    'adults' => $adults,
                    'roomQuantity' => 1,
                    'currency' => 'EUR'
                ],
                'headers' => ['Authorization' => "Bearer $token"]
            ]);

            $data = $response->toArray();
            return ['success' => true, 'offers' => $this->parseOffers($data)];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getCityCode(string $cityName): string
    {
        $normalized = strtolower(trim($cityName));
        return $this->cityCodeMap[$normalized] ?? strtoupper(substr($normalized, 0, 3));
    }

    private function parseHotels(array $data): array
    {
        $hotels = [];
        $items = $data['data'] ?? [];

        foreach ($items as $hotel) {
            $hotels[] = [
                'amadeus_id' => $hotel['hotelId'] ?? '',
                'nom' => $hotel['name'] ?? '',
                'type' => $hotel['chain'] ?? 'Hôtel',
                'adresse' => $this->formatAddress($hotel['address'] ?? []),
                'latitude' => $hotel['geoCode']['latitude'] ?? null,
                'longitude' => $hotel['geoCode']['longitude'] ?? null,
                'note' => $hotel['rating'] ?? 0,
                'equipements' => $this->extractAmenities($hotel),
                'images' => $this->extractImages($hotel),
                'contact' => $hotel['contact'] ?? []
            ];
        }

        return ['success' => true, 'hotels' => $hotels, 'total' => count($hotels)];
    }

    private function parseHotelDetails(array $data): array
    {
        $hotel = $data['data'] ?? $data;
        return [
            'amadeus_id' => $hotel['hotelId'] ?? '',
            'nom' => $hotel['name'] ?? '',
            'type' => $hotel['chain'] ?? 'Hôtel',
            'description' => $hotel['description'] ?? '',
            'adresse' => $this->formatAddress($hotel['address'] ?? []),
            'latitude' => $hotel['geoCode']['latitude'] ?? null,
            'longitude' => $hotel['geoCode']['longitude'] ?? null,
            'note' => $hotel['rating'] ?? 0,
            'equipements' => $this->extractAmenities($hotel),
            'images' => $this->extractImages($hotel),
            'contact' => $hotel['contact'] ?? [],
            'rooms' => $hotel['rooms'] ?? []
        ];
    }

    private function parseOffers(array $data): array
    {
        $offers = [];
        foreach ($data['data'] ?? [] as $offer) {
            $offers[] = [
                'offer_id' => $offer['offerId'] ?? '',
                'price' => $offer['offers'][0]['price']['total'] ?? 0,
                'currency' => $offer['offers'][0]['price']['currency'] ?? 'EUR',
                'rooms' => $offer['offers'][0]['rooms'] ?? []
            ];
        }
        return $offers;
    }

    private function formatAddress(array $address): string
    {
        $parts = [];
        if (!empty($address['lines'])) {
            $parts = $address['lines'];
        }
        if (!empty($address['cityName'])) {
            $parts[] = $address['cityName'];
        }
        if (!empty($address['countryCode'])) {
            $parts[] = $address['countryCode'];
        }
        return implode(', ', $parts);
    }

    private function extractAmenities(array $hotel): array
    {
        $amenities = [];
        foreach ($hotel['amenities'] ?? [] as $amenity) {
            $amenities[] = $amenity;
        }
        return $amenities;
    }

    private function extractImages(array $hotel): array
    {
        $images = [];
        foreach ($hotel['images'] ?? [] as $image) {
            $url = $image['url'] ?? '';
            if ($url) {
                $images[] = $url;
            }
        }
        return $images;
    }

    private function getMockHotels(string $cityCode): array
    {
        $cityNames = [
            'TUN' => 'Tunis',
            'PAR' => 'Paris',
            'LON' => 'London',
            'MAD' => 'Madrid',
            'DJE' => 'Djerba',
            'RAK' => 'Marrakech'
        ];

        $city = $cityNames[$cityCode] ?? $cityCode;
        $mockHotels = [
            [
                'amadeus_id' => 'ME' . strtolower($cityCode) . '001',
                'nom' => $city . ' Grand Hotel',
                'type' => 'Hôtel',
                'adresse' => '12 Rue de la Gare, ' . $city,
                'latitude' => 36.8 + rand(-1, 1) * 0.01,
                'longitude' => 10.1 + rand(-1, 1) * 0.01,
                'note' => 4.5,
                'equipements' => ['WiFi', 'Piscine', 'Restaurant', 'Spa', 'Parking'],
                'images' => ['https://placehold.co/600x400/3498db/ffffff?text=' . $city . '+Hotel'],
                'contact' => ['phone' => '+216 12 345 678', 'email' => 'info@hotel.tn']
            ],
            [
                'amadeus_id' => 'ME' . strtolower($cityCode) . '002',
                'nom' => $city . ' Boutique Hotel',
                'type' => 'Boutique',
                'adresse' => '25 Avenue Habib Bourguiba, ' . $city,
                'latitude' => 36.8 + rand(-1, 1) * 0.01,
                'longitude' => 10.1 + rand(-1, 1) * 0.01,
                'note' => 4.2,
                'equipements' => ['WiFi', 'Climatisation', 'Room Service'],
                'images' => ['https://placehold.co/600x400/e74c3c/ffffff?text=' . $city . '+Boutique'],
                'contact' => ['phone' => '+216 12 345 679', 'email' => 'contact@boutique.tn']
            ],
            [
                'amadeus_id' => 'ME' . strtolower($cityCode) . '003',
                'nom' => $city . ' Resort & Spa',
                'type' => 'Resort',
                'adresse' => '8 Zone Touristique, ' . $city,
                'latitude' => 36.8 + rand(-1, 1) * 0.01,
                'longitude' => 10.1 + rand(-1, 1) * 0.01,
                'note' => 4.8,
                'equipements' => ['WiFi', 'Piscine', 'Spa', 'Plage', 'Restaurant', 'Bar', 'Gym'],
                'images' => ['https://placehold.co/600x400/27ae60/ffffff?text=' . $city . '+Resort'],
                'contact' => ['phone' => '+216 12 345 680', 'email' => 'reservation@resort.tn']
            ]
        ];

        return ['success' => true, 'hotels' => $mockHotels, 'total' => count($mockHotels)];
    }
}