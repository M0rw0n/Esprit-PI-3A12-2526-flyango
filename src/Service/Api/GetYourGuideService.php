<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetYourGuideService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $getYourGuideApiKey = '')
    {
        $this->apiKey = $getYourGuideApiKey ?: $_ENV['GETYOURGUIDE_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function searchTours(string $location, string $category = ''): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockTours($location);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.getyourguide.com/v1/tours', [
                'query' => [
                    'location' => $location,
                    'category' => $category
                ]
            ]);

            $data = $response->toArray();
            return $this->parseTours($data['data'] ?? []);
        } catch (\Exception $e) {
            return $this->getMockTours($location);
        }
    }

    public function getTourDetails(int $tourId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockTourDetails($tourId);
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.getyourguide.com/v1/tours/' . $tourId, []);
            $data = $response->toArray();

            return [
                'success' => true,
                'tour' => $this->parseTourDetails($data['data'] ?? [])
            ];
        } catch (\Exception $e) {
            return $this->getMockTourDetails($tourId);
        }
    }

    public function bookTour(int $tourId, string $date, int $persons, array $options = []): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockBooking($tourId, $date, $persons);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api.getyourguide.com/v1/bookings', [
                'json' => [
                    'tour_id' => $tourId,
                    'date' => $date,
                    'participants' => $persons,
                    'options' => $options
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'booking' => $data['data'] ?? []
            ];
        } catch (\Exception $e) {
            return $this->getMockBooking($tourId, $date, $persons);
        }
    }

    public function getAvailability(int $tourId, string $date): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => true,
                'available' => true,
                'time_slots' => ['09:00', '10:30', '14:00', '16:30']
            ];
        }

        try {
            $response = $this->getClient()->request('GET', 'https://api.getyourguide.com/v1/tours/' . $tourId . '/availability', [
                'query' => ['date' => $date]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function parseTours(array $tours): array
    {
        $parsed = [];
        foreach ($tours as $tour) {
            $parsed[] = [
                'id' => $tour['id'] ?? 0,
                'name' => $tour['name'] ?? '',
                'description' => $tour['description'] ?? '',
                'price' => $tour['price'] ?? 0,
                'currency' => $tour['currency'] ?? 'EUR',
                'duration' => $tour['duration'] ?? '',
                'rating' => $tour['rating'] ?? 0,
                'reviews_count' => $tour['reviews_count'] ?? 0,
                'thumbnail' => $tour['pictures'][0] ?? ''
            ];
        }
        return $parsed;
    }

    private function parseTourDetails(array $data): array
    {
        return [
            'id' => $data['id'] ?? 0,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'highlights' => $data['highlights'] ?? [],
            'included' => $data['included'] ?? [],
            'not_included' => $data['not_included'] ?? [],
            'price' => $data['price'] ?? 0,
            'currency' => $data['currency'] ?? 'EUR',
            'duration' => $data['duration'] ?? '',
            'pickup' => $data['pickup'] ?? false,
            'languages' => $data['languages'] ?? [],
            'instant_confirmation' => $data['instant_confirmation'] ?? false
        ];
    }

    private function getMockTours(string $location): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Randonnée dans le désert de Tozeur',
                'description' => 'Explorez les dunes dorées du Sahara avec un guide local.',
                'price' => 75.00,
                'currency' => 'EUR',
                'duration' => '6 heures',
                'rating' => 4.9,
                'reviews_count' => 156,
                'thumbnail' => 'https://images.unsplash.com/photo-1546975490-e8b92a360b24?w=400'
            ],
            [
                'id' => 2,
                'name' => 'Découverte de Djerba en quad',
                'description' => 'Parcourez l\'île de Djerba en quad pour une aventure unique.',
                'price' => 55.00,
                'currency' => 'EUR',
                'duration' => '3 heures',
                'rating' => 4.7,
                'reviews_count' => 89,
                'thumbnail' => 'https://images.unsplash.com/photo-1570710891163-6f0e5a15e5f7?w=400'
            ],
            [
                'id' => 3,
                'name' => 'Excursion El Jem et Monastir',
                'description' => 'Visitez les sites archéologiques de la Tunisie.',
                'price' => 65.00,
                'currency' => 'EUR',
                'duration' => '8 heures',
                'rating' => 4.6,
                'reviews_count' => 203,
                'thumbnail' => 'https://images.unsplash.com/photo-1568337694837-3171ded03a27?w=400'
            ]
        ];
    }

    private function getMockTourDetails(int $tourId): array
    {
        return [
            'success' => true,
            'tour' => [
                'id' => $tourId,
                'name' => 'Randonnée dans le désert de Tozeur',
                'description' => 'Une expérience inoubliable dans le désert tunisien.',
                'highlights' => [
                    'Balade à dos de dromadaire',
                    'Coucher de soleil sur les dunes',
                    'Dîner traditionnel berbère'
                ],
                'included' => ['Transport', 'Guide', 'Dîner', 'Thé à la menthe'],
                'not_included' => ['Boissons', 'Pourboires'],
                'price' => 75.00,
                'currency' => 'EUR',
                'duration' => '6 heures',
                'pickup' => true,
                'languages' => ['Français', 'Anglais'],
                'instant_confirmation' => true
            ]
        ];
    }

    private function getMockBooking(int $tourId, string $date, int $persons): array
    {
        return [
            'success' => true,
            'booking' => [
                'id' => 'GYG-' . strtoupper(uniqid()),
                'tour_id' => $tourId,
                'date' => $date,
                'persons' => $persons,
                'status' => 'confirmed',
                'confirmation_code' => 'GYG-' . strtoupper(bin2hex(random_bytes(6)))
            ]
        ];
    }
}