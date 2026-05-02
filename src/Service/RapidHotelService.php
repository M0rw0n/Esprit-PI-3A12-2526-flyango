<?php

namespace App\Service;

class RapidHotelService
{
    private string $apiKey = 'sand_c0155ab8-c683-4f26-8f94-b5e92c5797b9';
    private string $baseUrl = 'https://api.liteapi.travel/v3.0';

    private static array $cityCountryMap = [
        'tunis' => ['code' => 'TN', 'name' => 'Tunis'],
        'djerba' => ['code' => 'TN', 'name' => 'Djerba'],
        'midoun' => ['code' => 'TN', 'name' => 'Djerba'],
        'houmt souk' => ['code' => 'TN', 'name' => 'Djerba'],
        'hammamet' => ['code' => 'TN', 'name' => 'Hammamet'],
        'sousse' => ['code' => 'TN', 'name' => 'Sousse'],
        'monastir' => ['code' => 'TN', 'name' => 'Monastir'],
        'marrakech' => ['code' => 'MA', 'name' => 'Marrakech'],
        'essaouira' => ['code' => 'MA', 'name' => 'Essaouira'],
        'paris' => ['code' => 'FR', 'name' => 'Paris'],
        'london' => ['code' => 'GB', 'name' => 'London'],
        'barcelona' => ['code' => 'ES', 'name' => 'Barcelona'],
        'rome' => ['code' => 'IT', 'name' => 'Rome'],
        'berlin' => ['code' => 'DE', 'name' => 'Berlin'],
        'amsterdam' => ['code' => 'NL', 'name' => 'Amsterdam'],
        'madrid' => ['code' => 'ES', 'name' => 'Madrid'],
        'istanbul' => ['code' => 'TR', 'name' => 'Istanbul'],
        'new york' => ['code' => 'US', 'name' => 'New York'],
        'dubai' => ['code' => 'AE', 'name' => 'Dubai'],
        'bali' => ['code' => 'ID', 'name' => 'Bali'],
        'tokyo' => ['code' => 'JP', 'name' => 'Tokyo'],
    ];

    public function getHotels(string $location, int $limit = 10): array
    {
        $normalized = strtolower($location);
        $cityData = self::$cityCountryMap[$normalized] ?? ['code' => '', 'name' => $location];

        if (empty($cityData['code'])) {
            return $this->getStaticHotels($location);
        }

        $url = $this->baseUrl . '/data/hotels?' . http_build_query([
            'countryCode' => $cityData['code'],
            'cityName' => $cityData['name'],
            'limit' => $limit,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-API-Key: {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return $this->getStaticHotels($location);
        }

        $data = json_decode($response, true);

        if (empty($data['data'])) {
            return $this->getStaticHotels($location);
        }

        $hotels = [];
        foreach ($data['data'] as $hotel) {
            $hotels[] = $this->parseHotel($hotel);
        }

        return [
            'success' => true,
            'hotels' => $hotels,
            'total' => count($hotels),
            'source' => 'api',
            'location' => $location,
        ];
    }

    private function parseHotel(array $h): array
    {
        return [
            'nom' => $h['name'],
            'ville' => $h['city'],
            'adresse' => $h['address'],
            'prix' => rand(50, 300),
            'imageUrl' => $h['main_photo'] ?? $h['thumbnail'] ?? $this->getHotelImage($h['name']),
            'note' => ($h['rating'] ?? 0) / 2,
            'equipements' => [],
            'lat' => $h['latitude'] ?? null,
            'lng' => $h['longitude'] ?? null,
            'pays' => strtoupper($h['country'] ?? ''),
            'stars' => $h['stars'] ?? 0,
            'thumbnail' => $h['thumbnail'] ?? '',
        ];
    }

    private function getHotelImage(string $hotelName): string
    {
        $images = [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800',
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
            'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800',
            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
            'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800',
        ];

        return $images[array_rand($images)];
    }

    public function getStaticHotels(string $city): array
    {
        $images = [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800',
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
            'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800',
            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
        ];

        $equipments = [
            ['WiFi', 'Piscine', 'Restaurant', 'Parking'],
            ['WiFi', 'Spa', 'Gym', 'Bar'],
            ['WiFi', 'Room Service', 'Réception 24h'],
        ];

        $hotels = [];
        for ($i = 1; $i <= 10; $i++) {
            $hotels[] = [
                'nom' => ucfirst($city) . ' Hotel ' . $i,
                'ville' => ucfirst($city),
                'adresse' => 'Hotel Street ' . $i . ', ' . ucfirst($city),
                'prix' => rand(50, 300),
                'imageUrl' => $images[array_rand($images)],
                'note' => rand(35, 50) / 10,
                'equipements' => $equipments[array_rand($equipments)],
            ];
        }

        return [
            'success' => true,
            'hotels' => $hotels,
            'total' => count($hotels),
            'source' => 'static',
            'location' => $city,
        ];
    }
}