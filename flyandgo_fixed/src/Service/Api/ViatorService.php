<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ViatorService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;
    private string $affiliateId;

    public function __construct(
        string $viatorApiKey = '',
        string $viatorAffiliateId = ''
    ) {
        $this->apiKey = $viatorApiKey ?: $_ENV['VIATOR_API_KEY'] ?? '';
        $this->affiliateId = $viatorAffiliateId ?: $_ENV['VIATOR_AFFILIATE_ID'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchProducts(string $location, string $category = ''): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockProducts($location);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://viatorapi.viator.com/service/product/search', [
                'query' => [
                    'location' => $location,
                    'category' => $category,
                    'currency' => 'EUR'
                ]
            ]);

            $data = $response->toArray();
            return $this->parseProducts($data['data'] ?? []);
        } catch (\Exception $e) {
            return $this->getMockProducts($location);
        }
    }

    public function getProductDetails(string $productCode): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockProductDetails($productCode);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://viatorapi.viator.com/service/product/details/' . $productCode, []);
            $data = $response->toArray();

            return [
                'success' => true,
                'product' => $this->parseProductDetails($data)
            ];
        } catch (\Exception $e) {
            return $this->getMockProductDetails($productCode);
        }
    }

    public function bookProduct(string $productCode, string $date, int $persons, array $travelerInfo = []): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockBooking($productCode, $date, $persons);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://viatorapi.viator.com/service/booking/book', [
                'json' => [
                    'productCode' => $productCode,
                    'travelDate' => $date,
                    'pax' => $persons,
                    'travelers' => $travelerInfo,
                    'affiliateId' => $this->affiliateId
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'booking' => $data
            ];
        } catch (\Exception $e) {
            return $this->getMockBooking($productCode, $date, $persons);
        }
    }

    public function getAvailability(string $productCode, string $date): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => true,
                'available' => true,
                'slots' => ['09:00', '10:00', '14:00', '16:00']
            ];
        }

        try {
            $response = $this->getClient()->request('GET', 'https://viatorapi.viator.com/service/product/availability', [
                'query' => [
                    'productCode' => $productCode,
                    'date' => $date
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function parseProducts(array $products): array
    {
        $parsed = [];
        foreach ($products as $product) {
            $parsed[] = [
                'code' => $product['code'] ?? '',
                'title' => $product['title'] ?? '',
                'description' => $product['description'] ?? '',
                'price' => $product['price'] ?? 0,
                'currency' => $product['currency'] ?? 'EUR',
                'duration' => $product['duration'] ?? '',
                'rating' => $product['rating'] ?? 0,
                'thumbnail' => $product['thumbnail'] ?? ''
            ];
        }
        return $parsed;
    }

    private function parseProductDetails(array $data): array
    {
        return [
            'code' => $data['code'] ?? '',
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'highlights' => $data['highlights'] ?? [],
            'inclusions' => $data['inclusions'] ?? [],
            'exclusions' => $data['exclusions'] ?? [],
            'price' => $data['price'] ?? 0,
            'currency' => $data['currency'] ?? 'EUR',
            'duration' => $data['duration'] ?? '',
            'pickup' => $data['pickup'] ?? false,
            'languages' => $data['languages'] ?? []
        ];
    }

    private function getMockProducts(string $location): array
    {
        return [
            [
                'code' => 'VIATOR-MOCK-1',
                'title' => 'Excursion à Djerba - Demi journée',
                'description' => 'Découvrez les beautés de Djerba avec ce circuit guidée.',
                'price' => 45.00,
                'currency' => 'EUR',
                'duration' => '4 heures',
                'rating' => 4.5,
                'thumbnail' => 'https://images.unsplash.com/photo-1570710891163-6f0e5a15e5f7?w=400'
            ],
            [
                'code' => 'VIATOR-MOCK-2',
                'title' => 'Safari désert à Tozeur',
                'description' => 'Expérience inoubliable dans le désert tunisien.',
                'price' => 89.00,
                'currency' => 'EUR',
                'duration' => '1 jour',
                'rating' => 4.8,
                'thumbnail' => 'https://images.unsplash.com/photo-1546975490-e8b92a360b24?w=400'
            ],
            [
                'code' => 'VIATOR-MOCK-3',
                'title' => 'Visite guidée de Carthage',
                'description' => 'Plongez dans l\'histoire de la Carthage antique.',
                'price' => 35.00,
                'currency' => 'EUR',
                'duration' => '3 heures',
                'rating' => 4.3,
                'thumbnail' => 'https://images.unsplash.com/photo-1568337694837-3171ded03a27?w=400'
            ]
        ];
    }

    private function getMockProductDetails(string $productCode): array
    {
        return [
            'success' => true,
            'product' => [
                'code' => $productCode,
                'title' => 'Excursion Demo - Djerba',
                'description' => 'Une excursion inoubliable pour découvrir les merveilles de la Tunisie.',
                'highlights' => [
                    'Guide professionnel',
                    'Transport inclus',
                    'Déjeuner traditionnel'
                ],
                'inclusions' => ['Transport', 'Guide', 'Déjeuner'],
                'exclusions' => ['Boissons', 'Pourboires'],
                'price' => 45.00,
                'currency' => 'EUR',
                'duration' => '4 heures',
                'pickup' => true,
                'languages' => ['Français', 'Anglais', 'Arabe']
            ]
        ];
    }

    private function getMockBooking(string $productCode, string $date, int $persons): array
    {
        return [
            'success' => true,
            'booking' => [
                'bookingId' => 'VIATOR-' . strtoupper(uniqid()),
                'productCode' => $productCode,
                'date' => $date,
                'persons' => $persons,
                'status' => 'CONFIRMED',
                'confirmationCode' => 'CONF-' . strtoupper(bin2hex(random_bytes(6)))
            ]
        ];
    }
}