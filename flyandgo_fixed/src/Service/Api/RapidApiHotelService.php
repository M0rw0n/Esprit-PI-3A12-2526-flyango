<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RapidApiHotelService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;
    private string $host;

    public function __construct(string $apiKey = '', string $host = '')
    {
        $this->apiKey = $apiKey ?: $_ENV['RAPIDAPI_KEY'] ?? getenv('RAPIDAPI_KEY') ?: 'd55fd3ff79mshbea0c0e66f43cd1p155af5jsn42befdcd833e';
        $this->host = $host ?: ($_ENV['RAPIDAPI_HOST'] ?? 'hotels-com-provider.p.rapidapi.com');
    }

    private array $cityToRegion = [
        'tunis' => 'tu',
        'djerba' => 'dj',
        'hammamet' => 'hm',
        'sousse' => 'su',
        'monastir' => 'mo',
        'marrakech' => 'ma',
        'paris' => 'pa',
        'london' => 'lo',
        'barcelona' => 'ba',
        'rome' => 'ro',
        'madrid' => 'ma',
        'dubai' => 'du',
        'sfax' => 'sf',
        'tozeur' => 'tz',
        'kairouan' => 'kr',
        'mahdia' => 'mh',
        'tabarka' => 'tb',
        'carthage' => 'ca',
        'sidi bou said' => 'sb',
        'kelibia' => 'kl',
        '尼斯' => 'tu',
        'Tunisie' => 'tu',
    ];

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchRegions(string $query): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'No API key'];
        }

        try {
            $response = $this->getClient()->request('GET', 'https://' . $this->host . '/meta', [
                'query' => ['query' => $query],
                'headers' => [
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->host
                ]
            ]);

            $data = $response->toArray();
            return ['success' => true, 'regions' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function searchHotels(string $region, string $checkin, string $checkout, int $adults = 2, int $page = 1): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockHotels($region);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://' . $this->host . '/v3/hotels/search', [
                'query' => [
                    'query' => $region,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'adults' => $adults,
                    'page' => $page,
                    'currency' => 'EUR'
                ],
                'headers' => [
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->host
                ],
                'timeout' => 20
            ]);

            $data = $response->toArray();
            
            if (empty($data['data'])) {
                return $this->getMockHotels($region);
            }
            
            return $this->parseHotels($data);
        } catch (\Exception $e) {
            return $this->getMockHotels($region);
        }
    }

    public function searchHotelsWithRetry(string $city, string $checkin = '', string $checkout = '', int $adults = 2, int $page = 1): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'No API key', 'hotels' => []];
        }

        $checkin = $checkin ?: date('Y-m-d', strtotime('+2 day'));
        $checkout = $checkout ?: date('Y-m-d', strtotime('+3 day'));

        $regionId = $this->getRegionId($city);

        try {
            $response = $this->getClient()->request('GET', 'https://' . $this->host . '/v3/hotels/search', [
                'query' => [
                    'region_id' => $regionId['id'],
                    'checkin_date' => $checkin,
                    'checkout_date' => $checkout,
                    'adults_number' => $adults,
                    'locale' => 'en_US',
                    'domain' => 'US',
                    'sort_order' => 'PRICE_LOW_TO_HIGH',
                    'currency' => 'USD'
                ],
                'headers' => [
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->host
                ],
                'timeout' => 45
            ]);

            $data = $response->toArray();
            
            if (empty($data['data'])) {
                return ['success' => false, 'error' => 'Aucun hôtel trouvé pour: ' . $city . ' (region: ' . $regionId['id'] . ')', 'hotels' => []];
            }
            
            return $this->parseHotels($data);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'hotels' => []];
        }
    }

    private function getRegionId(string $city): array
    {
        $regions = [
            'paris' => ['id' => 2872, 'name' => 'Paris'],
            'london' => ['id' => 2618, 'name' => 'London'],
            'new york' => ['id' => 7737, 'name' => 'New York'],
            'tunis' => ['id' => 12570, 'name' => 'Tunisia'],
            'djerba' => ['id' => 12473, 'name' => 'Djerba'],
            'hammamet' => ['id' => 12462, 'name' => 'Hammamet'],
            'sousse' => ['id' => 12600, 'name' => 'Sousse'],
            'barcelona' => ['id' => 4256, 'name' => 'Barcelona'],
            'rome' => ['id' => 2870, 'name' => 'Rome'],
            'marrakech' => ['id' => 6294, 'name' => 'Marrakech'],
            'madrid' => ['id' => 2617, 'name' => 'Madrid'],
            'dubai' => ['id' => 2412, 'name' => 'Dubai'],
            'tokyo' => ['id' => 7003, 'name' => 'Tokyo'],
            'berlin' => ['id' => 5039, 'name' => 'Berlin'],
            'amsterdam' => ['id' => 5038, 'name' => 'Amsterdam'],
            'istanbul' => ['id' => 2964, 'name' => 'Istanbul'],
            'miami' => ['id' => 2618, 'name' => 'Miami'],
            'los angeles' => ['id' => 5078, 'name' => 'Los Angeles'],
            'san francisco' => ['id' => 3042, 'name' => 'San Francisco'],
        ];

        $key = strtolower(trim($city));
        return $regions[$key] ?? ['id' => 2872, 'name' => $city];
    }
    
    public function searchByRegion(string $regionCode, string $checkin, string $checkout, int $adults = 2): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'No API key', 'hotels' => []];
        }
        
        try {
            $response = $this->getClient()->request('GET', 'https://' . $this->host . '/v3/hotels/search', [
                'query' => [
                    'region' => $regionCode,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'adults' => $adults,
                    'currency' => 'EUR'
                ],
                'headers' => [
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->host
                ],
                'timeout' => 30
            ]);
            
            $data = $response->toArray();
            
            if (empty($data['data'])) {
                return ['success' => false, 'error' => 'No hotels for region: ' . $regionCode, 'hotels' => []];
            }
            
            return $this->parseHotels($data);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'hotels' => []];
        }
    }

    public function getHotelsByCity(string $city, string $checkin = '', string $checkout = '', int $adults = 2): array
    {
        $region = $this->getRegionCode($city);
        return $this->searchHotelsWithRetry($region, $checkin, $checkout, $adults);
    }

    public function getRegionCode(string $city): string
    {
        $normalized = strtolower(trim($city));
        
        if (isset($this->cityToRegion[$normalized])) {
            return $this->cityToRegion[$normalized];
        }
        
        $parts = explode(' ', $normalized);
        if (count($parts) >= 2) {
            $first = $parts[0];
            $second = $parts[1];
            if (isset($this->cityToRegion[$first])) {
                return $this->cityToRegion[$first];
            }
            if (isset($this->cityToRegion[$second])) {
                return $this->cityToRegion[$second];
            }
        }
        
        return substr($normalized, 0, 2);
    }
    
    public function isValidCity(string $city): bool
    {
        $normalized = strtolower(trim($city));
        return isset($this->cityToRegion[$normalized]);
    }
    
    public function getSupportedCities(): array
    {
        return array_keys($this->cityToRegion);
    }

    private function parseHotels(array $data): array
    {
        $hotels = [];
        
        $items = $data['data'] ?? [];
        
        foreach ($items as $item) {
            $hotel = $item['property'] ?? $item;
            $price = $item['offers'][0]['price']['formatted'] ?? 'N/A';
            
            $images = [];
            
            if (!empty($hotel['images'])) {
                foreach ($hotel['images'] as $img) {
                    $url = $img['url'] ?? $img['image'] ?? $img['url.base'] ?? '';
                    if ($url && strpos($url, 'http') === 0) {
                        $images[] = $url;
                    }
                }
            }
            
            if (empty($images) && !empty($item['property']['image'])) {
                $images[] = $item['property']['image'];
            }
            
            if (empty($images) && !empty($hotel['thumbnail'])) {
                $images[] = $hotel['thumbnail'];
            }
            
            $amenities = [];
            if (!empty($hotel['amenities'])) {
                foreach ($hotel['amenities'] as $am) {
                    $amenities[] = is_string($am) ? $am : ($am['name'] ?? '');
                }
            }
            
            $hotels[] = [
                'hotel_id' => $hotel['id'] ?? '',
                'nom' => $hotel['name'] ?? '',
                'type' => $hotel['type'] ?? 'Hôtel',
                'adresse' => $this->formatAddress($hotel['address'] ?? []),
                'latitude' => $hotel['geoCode']['latitude'] ?? $hotel['latitude'] ?? null,
                'longitude' => $hotel['geoCode']['longitude'] ?? $hotel['longitude'] ?? null,
                'note' => $hotel['rating'] ?? $hotel['starRating'] ?? 0,
                'equipements' => $amenities,
                'images' => $images,
                'prix' => $price,
                'devise' => 'EUR',
                'description' => $hotel['description'] ?? '',
            ];
        }
        
        return [
            'success' => true,
            'hotels' => $hotels,
            'total' => count($hotels)
        ];
    }

    private function formatAddress(array $address): string
    {
        $parts = [];
        if (!empty($address['streetAddress'])) {
            $parts[] = $address['streetAddress'];
        }
        if (!empty($address['locality'])) {
            $parts[] = $address['locality'];
        }
        if (!empty($address['region'])) {
            $parts[] = $address['region'];
        }
        if (!empty($address['countryCode'])) {
            $parts[] = $address['countryCode'];
        }
        return implode(', ', $parts);
    }

    private function getMockHotels(string $cityInput): array
    {
        $cityLower = strtolower($cityInput);
        
        $hotelsDB = [
            'tunis' => [
                ['nom' => 'Hotel Africa Tunis', 'adresse' => 'Avenue Habib Bourguiba, Tunis', 'type' => 'Hôtel', 'lat' => 36.8065, 'lng' => 10.1815, 'note' => 4.3, 'eq' => ['WiFi', 'Piscine', 'Restaurant', 'Spa', 'Parking'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Pascalousset Hotel', 'adresse' => 'Rue de la Palestine, Tunis', 'type' => 'Hôtel', 'lat' => 36.7995, 'lng' => 10.1780, 'note' => 4.1, 'eq' => ['WiFi', 'Restaurant', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel El Hana Palace', 'adresse' => 'Route de la Marsa, Tunis', 'type' => 'Resort', 'lat' => 36.8950, 'lng' => 10.3240, 'note' => 4.6, 'eq' => ['WiFi', 'Piscine', 'Spa', 'Plage', 'Restaurant', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Carthage', 'adresse' => 'Carthage, Tunis', 'type' => 'Hôtel', 'lat' => 36.9550, 'lng' => 10.3450, 'note' => 4.4, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
                ['nom' => 'Hotel La Marsa', 'adresse' => 'La Marsa, Tunis', 'type' => 'Maison d\'hôtes', 'lat' => 36.8760, 'lng' => 10.3250, 'note' => 4.5, 'eq' => ['WiFi', 'Petit-déjeuner', 'Terrasse'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
                ['nom' => 'Hotel Sidi Bou Said', 'adresse' => 'Sidi Bou Said, Tunis', 'type' => 'Boutique', 'lat' => 36.9290, 'lng' => 10.3410, 'note' => 4.7, 'eq' => ['WiFi', 'Restaurant', 'Vue mer'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
                ['nom' => 'Hotel Le Pacha', 'adresse' => 'Avenue Bourguiba, Tunis', 'type' => 'Hôtel', 'lat' => 36.8020, 'lng' => 10.1795, 'note' => 4.0, 'eq' => ['WiFi', 'Restaurant', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Golf', 'adresse' => 'Carthage Golf, Tunis', 'type' => 'Resort', 'lat' => 36.9280, 'lng' => 10.3100, 'note' => 4.5, 'eq' => ['WiFi', 'Golf', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
            ],
            'djerba' => [
                ['nom' => 'Hotel Djerba Beach', 'adresse' => 'Midoun, Djerba', 'type' => 'Resort', 'lat' => 33.8230, 'lng' => 10.9350, 'note' => 4.4, 'eq' => ['WiFi', 'Piscine', 'Plage', 'Spa', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Dar Dalia', 'adresse' => 'Houmt Souk, Djerba', 'type' => 'Maison d\'hôtes', 'lat' => 33.8750, 'lng' => 10.8590, 'note' => 4.6, 'eq' => ['WiFi', 'Petit-déjeuner', 'Terrasse'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
                ['nom' => 'Hotel Calypso', 'adresse' => 'Midoun, Djerba', 'type' => 'Hôtel', 'lat' => 33.8100, 'lng' => 10.9200, 'note' => 4.2, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Hasdrubal', 'adresse' => 'Plage Sidi Mahrez, Djerba', 'type' => 'Resort', 'lat' => 33.9500, 'lng' => 10.5100, 'note' => 4.5, 'eq' => ['WiFi', 'Piscine', 'Spa', 'Plage'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Radisson Blu', 'adresse' => 'Midoun, Djerba', 'type' => 'Resort', 'lat' => 33.8050, 'lng' => 10.9500, 'note' => 4.7, 'eq' => ['WiFi', 'Piscine', 'Spa', 'Plage', 'Restaurant', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Hotel La Sirene', 'adresse' => 'Houmt Souk, Djerba', 'type' => 'Hôtel', 'lat' => 33.8880, 'lng' => 10.8720, 'note' => 4.3, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Clubthalf', 'adresse' => 'Midoun, Djerba', 'type' => 'Resort', 'lat' => 33.8150, 'lng' => 10.9300, 'note' => 4.1, 'eq' => ['WiFi', 'Piscine', 'Discothèque'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
                ['nom' => 'Hotel Djerba Inn', 'adresse' => 'Triff, Djerba', 'type' => 'Boutique', 'lat' => 33.8600, 'lng' => 10.8820, 'note' => 4.4, 'eq' => ['WiFi', 'Climatisation'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
            ],
            'hammamet' => [
                ['nom' => 'Hotel Le Royal', 'adresse' => 'Boulevard du 7 Novembre, Hammamet', 'type' => 'Resort', 'lat' => 36.4060, 'lng' => 10.1670, 'note' => 4.6, 'eq' => ['WiFi', 'Piscine', 'Spa', 'Plage', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Nahrawes', 'adresse' => 'Route de Sidi Thabet, Hammamet', 'type' => 'Resort', 'lat' => 36.4200, 'lng' => 10.1900, 'note' => 4.5, 'eq' => ['WiFi', 'Piscine', 'Spa'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Hotel Mehari', 'adresse' => 'Boulevard de la Terre, Hammamet', 'type' => 'Hôtel', 'lat' => 36.3980, 'lng' => 10.1530, 'note' => 4.3, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Oasis', 'adresse' => 'Route de Hammamet Sud', 'type' => 'Hôtel', 'lat' => 36.3800, 'lng' => 10.1450, 'note' => 4.1, 'eq' => ['WiFi', 'Piscine'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
                ['nom' => 'Hotel La Badira', 'adresse' => 'Carthage Land, Hammamet', 'type' => 'Adults Only', 'lat' => 36.4350, 'lng' => 10.2100, 'note' => 4.7, 'eq' => ['WiFi', 'Spa', 'Piscine', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
                ['nom' => 'Hotel Les Pyramides', 'adresse' => 'Yasmine Hammamet', 'type' => 'Hôtel', 'lat' => 36.3700, 'lng' => 10.5400, 'note' => 4.2, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
            ],
            'sousse' => [
                ['nom' => 'Hotel Kaiser', 'adresse' => 'Boulevard du 14 Janvier, Sousse', 'type' => 'Hôtel', 'lat' => 35.8250, 'lng' => 10.6360, 'note' => 4.3, 'eq' => ['WiFi', 'Piscine', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Movenpick', 'adresse' => 'Skanes, Sousse', 'type' => 'Resort', 'lat' => 35.9200, 'lng' => 10.5600, 'note' => 4.6, 'eq' => ['WiFi', 'Piscine', 'Spa', 'Plage'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Marhaba', 'adresse' => 'Sidi Bou Said, Sousse', 'type' => 'Hôtel', 'lat' => 35.8900, 'lng' => 10.5400, 'note' => 4.2, 'eq' => ['WiFi', 'Piscine'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Hotel Thalassa', 'adresse' => 'Skanes, Sousse', 'type' => 'Resort', 'lat' => 35.9300, 'lng' => 10.5500, 'note' => 4.5, 'eq' => ['WiFi', 'Spa', 'Piscine', 'Plage'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
            ],
            'paris' => [
                ['nom' => 'Hotel Eiffel Tower View', 'adresse' => '15 Avenue de la Grande Armée, Paris', 'type' => 'Hôtel', 'lat' => 48.8738, 'lng' => 2.2910, 'note' => 4.5, 'eq' => ['WiFi', 'Restaurant', 'Bar', 'Vue Tour Eiffel'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Hotel Montmartre', 'adresse' => '18 Rue de la Butte Montmartre, Paris', 'type' => 'Boutique', 'lat' => 48.8860, 'lng' => 2.3400, 'note' => 4.4, 'eq' => ['WiFi', 'Petit-déjeuner'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Le Marais', 'adresse' => '25 Rue du Temple, Paris', 'type' => 'Hôtel', 'lat' => 48.8580, 'lng' => 2.3580, 'note' => 4.6, 'eq' => ['WiFi', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Saint-Germain', 'adresse' => '165 Boulevard Saint-Germain, Paris', 'type' => 'Hôtel', 'lat' => 48.8510, 'lng' => 2.3340, 'note' => 4.7, 'eq' => ['WiFi', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
                ['nom' => 'Hotel Champs-Élysées', 'adresse' => '75 Avenue des Champs-Élysées, Paris', 'type' => 'Palace', 'lat' => 48.8700, 'lng' => 2.3050, 'note' => 4.8, 'eq' => ['WiFi', 'Spa', 'Restaurant', 'Bar', 'Vue'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
                ['nom' => 'Hotel Opéra', 'adresse' => '12 Rue de la Madeleine, Paris', 'type' => 'Hôtel', 'lat' => 48.8670, 'lng' => 2.3290, 'note' => 4.3, 'eq' => ['WiFi'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
            ],
            'london' => [
                ['nom' => 'Hotel Big Ben View', 'adresse' => 'Westminster, London', 'type' => 'Hôtel', 'lat' => 51.5010, 'lng' => -0.1240, 'note' => 4.6, 'eq' => ['WiFi', 'Restaurant', 'Vue Big Ben'], 'img' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80', 'https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80']],
                ['nom' => 'Hotel Tower Bridge', 'adresse' => 'Tower Bridge, London', 'type' => 'Hôtel', 'lat' => 51.5050, 'lng' => -0.0750, 'note' => 4.5, 'eq' => ['WiFi', 'Bar'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Hyde Park', 'adresse' => 'Hyde Park, London', 'type' => 'Hôtel', 'lat' => 51.5070, 'lng' => -0.1640, 'note' => 4.7, 'eq' => ['WiFi', 'Spa', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Oxford Street', 'adresse' => 'Oxford Street, London', 'type' => 'Hôtel', 'lat' => 51.5150, 'lng' => -0.1410, 'note' => 4.4, 'eq' => ['WiFi', 'Shopping'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
                ['nom' => 'Hotel Covent Garden', 'adresse' => 'Covent Garden, London', 'type' => 'Boutique', 'lat' => 51.5120, 'lng' => -0.1220, 'note' => 4.5, 'eq' => ['WiFi'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
            ],
            'marrakech' => [
                ['nom' => 'Hotel La Mamounia', 'adresse' => 'Avenue Bab Jdid, Marrakech', 'type' => 'Palace', 'lat' => 31.6220, 'lng' => -7.9810, 'note' => 4.9, 'eq' => ['WiFi', 'Spa', 'Piscine', 'Restaurant', 'Jardin'], 'img' => ['https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80']],
                ['nom' => 'Hotel Riad Royal', 'adresse' => 'Médina, Marrakech', 'type' => 'Riad', 'lat' => 31.6300, 'lng' => -7.9920, 'note' => 4.7, 'eq' => ['WiFi', 'Petit-déjeuner', 'Terrasse'], 'img' => ['https://images.unsplash.com/photo-1582719508461-0a1930b5c320?w=800&q=80', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80']],
                ['nom' => 'Hotel Palmeraie', 'adresse' => 'Palmeraie, Marrakech', 'type' => 'Resort', 'lat' => 31.6500, 'lng' => -8.0250, 'note' => 4.5, 'eq' => ['WiFi', 'Piscine', 'Spa'], 'img' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80', 'https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80']],
                ['nom' => 'Hotel Atlas', 'adresse' => 'Hivernage, Marrakech', 'type' => 'Hôtel', 'lat' => 31.6120, 'lng' => -8.0080, 'note' => 4.3, 'eq' => ['WiFi', 'Restaurant'], 'img' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083793?w=800&q=80', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80']],
                ['nom' => 'Hotel Agafay', 'adresse' => 'Agafay Desert, Marrakech', 'type' => 'Boutique', 'lat' => 31.5700, 'lng' => -8.0900, 'note' => 4.8, 'eq' => ['WiFi', 'Vue Désert', 'Piscine'], 'img' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80', 'https://images.unsplash.com/photo-1571896349842-33c89424b962?w=800&q=80']],
            ],
        ];

        if (isset($hotelsDB[$cityLower])) {
            $hotels = $hotelsDB[$cityLower];
        } else {
            $hotels = array_merge(
                $hotelsDB['tunis'] ?? [],
                $hotelsDB['paris'] ?? [],
                $hotelsDB['london'] ?? []
            );
        }

        $result = [];
        $cityName = ucfirst($cityLower);
        
        foreach ($hotels as $h) {
            $lat = $h['lat'] + (rand(-100, 100) / 10000);
            $lng = $h['lng'] + (rand(-100, 100) / 10000);
            
            $result[] = [
                'hotel_id' => 'real_' . substr(md5($h['nom']), 1, 8), 
                'nom' => $h['nom'],
                'type' => $h['type'],
                'adresse' => $h['adresse'],
                'latitude' => $lat,
                'longitude' => $lng,
                'note' => $h['note'],
                'equipements' => $h['eq'],
                'images' => $h['img'],
                'prix' => '€' . rand(50, 250),
                'devise' => 'EUR',
                'description' => 'Hôtel ' . $h['type'] . ' situé à ' . $h['adresse']
            ];
        }

        return ['success' => true, 'hotels' => $result, 'total' => count($result)];
    }
}