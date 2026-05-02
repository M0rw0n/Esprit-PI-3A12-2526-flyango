<?php

namespace App\Service\Api;

use App\Repository\HebergementRepository;
use App\Repository\CircuitRepository;
use App\Repository\ActivityRepository;

class ExternalApiService
{
    private HebergementRepository $hebergementRepo;
    private CircuitRepository $circuitRepo;
    private ActivityRepository $activityRepo;

    public function __construct(
        HebergementRepository $hebergementRepository,
        CircuitRepository $circuitRepository,
        ActivityRepository $activityRepository
    ) {
        $this->hebergementRepo = $hebergementRepository;
        $this->circuitRepo = $circuitRepository;
        $this->activityRepo = $activityRepository;
    }

    public function search(string $type, array $params): array
    {
        return match($type) {
            'hotel' => $this->searchHotels(
                $params['city'] ?? '',
                $params['check_in'] ?? date('Y-m-d', strtotime('+7 days')),
                $params['check_out'] ?? date('Y-m-d', strtotime('+10 days')),
                $params['guests'] ?? 1,
                $params['rooms'] ?? 1
            ),
            'flight' => $this->searchFlights(
                $params['origin'] ?? 'TUN',
                $params['destination'] ?? '',
                $params['date'] ?? date('Y-m-d', strtotime('+7 days')),
                $params['passengers'] ?? 1
            ),
            'car' => $this->searchCars(
                $params['location'] ?? '',
                $params['pickup_date'] ?? date('Y-m-d'),
                $params['dropoff_date'] ?? date('Y-m-d', strtotime('+3 days'))
            ),
            default => [
                'success' => false,
                'error' => 'Invalid search type',
                'results' => []
            ]
        };
    }

    public function searchHotels(string $city, string $checkIn, string $checkOut, int $guests = 1, int $rooms = 1): array
    {
        try {
            $hotels = $this->hebergementRepo->findAll();
            $results = [];

            foreach ($hotels as $hotel) {
                if (!$hotel instanceof \App\Entity\Hebergement) continue;
                if (!$hotel->isDisponible()) continue;

                $price = $hotel->getPrixParNuit();
                if ($price <= 0) continue;

                $originalPrice = round($price * 1.2);
                $rating = $hotel->getMoyenneNotes() > 0 ? $hotel->getMoyenneNotes() : 8.0;

                $results[] = [
                    'id' => 'HOTEL_' . $hotel->getId(),
                    'type' => 'hotel',
                    'title' => $hotel->getNom() ?? 'Hôtel',
                    'location' => $hotel->getVille() ?? '',
                    'city' => $hotel->getVille() ?? '',
                    'rating' => (float) $rating,
                    'stars' => 4,
                    'price' => (float) $price,
                    'original_price' => (float) $originalPrice,
                    'currency' => 'TND',
                    'provider' => 'Fly&Go',
                    'provider_logo' => '',
                    'image' => $hotel->getImage() ? '/uploads/hebergements/' . $hotel->getImage() : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400',
                    'link' => '/hebergements/' . $hotel->getId(),
                    'free_cancellation' => true,
                    'breakfast_included' => false,
                    'discount' => 0,
                    'guest_rating' => (float) $rating,
                    'review_count' => 50,
                    'description' => $hotel->getDescription() ?? '',
                    'amenities' => []
                ];
            }

            if (empty($results)) {
                return ['success' => false, 'error' => 'Aucun hébergement disponible', 'results' => [], 'provider' => null];
            }

            return ['success' => true, 'results' => $results, 'provider' => 'Fly&Go', 'search_info' => []];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Erreur: ' . $e->getMessage(), 'results' => [], 'provider' => null];
        }
    }

    public function searchFlights(string $origin, string $destination, string $date, int $passengers = 1): array
    {
        try {
            $circuits = $this->circuitRepo->findAll();
            $results = [];

            foreach ($circuits as $circuit) {
                if (!$circuit instanceof \App\Entity\Circuit) continue;
                if ($circuit->getStatus() !== 'actif') continue;

                $price = $circuit->getPrix();
                if ($price <= 0) continue;

                $originalPrice = round($price * 1.15);

                $results[] = [
                    'id' => 'CIRCUIT_' . $circuit->getId(),
                    'type' => 'flight',
                    'title' => $circuit->getTitre() ?? 'Circuit',
                    'airline' => 'Fly&Go',
                    'airline_code' => 'FG',
                    'origin' => $origin,
                    'destination' => $circuit->getDestination() ?? '',
                    'departure' => $date,
                    'arrival' => $date,
                    'duration' => ($circuit->getDuree() ?? 1) . ' jours',
                    'stops' => 0,
                    'price' => (float) $price,
                    'original_price' => (float) $originalPrice,
                    'currency' => 'TND',
                    'provider' => 'Fly&Go',
                    'provider_logo' => '',
                    'image' => $circuit->getImage() ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=400',
                    'link' => '/circuits/' . $circuit->getId(),
                    'seats_left' => 10,
                    'baggage_included' => true,
                    'class' => 'Guide inclus',
                    'discount' => 0
                ];
            }

            if (empty($results)) {
                return ['success' => false, 'error' => 'Aucun circuit disponible', 'results' => [], 'provider' => null];
            }

            return ['success' => true, 'results' => $results, 'provider' => 'Fly&Go', 'search_info' => []];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Erreur: ' . $e->getMessage(), 'results' => [], 'provider' => null];
        }
    }

    public function searchCars(string $location, string $pickupDate, string $dropoffDate): array
    {
        try {
            $activities = $this->activityRepo->findAll();
            $results = [];

            foreach ($activities as $activity) {
                if (!$activity instanceof \App\Entity\Activity) continue;
                if (!$activity->isActif()) continue;

                $price = $activity->getPrice();
                if ($price <= 0) continue;

                $originalPrice = round($price * 1.1);

                $results[] = [
                    'id' => 'ACTIVITY_' . $activity->getId(),
                    'type' => 'car',
                    'title' => $activity->getTitle() ?? 'Activité',
                    'brand' => 'Activité',
                    'model' => $activity->getDuration() ?? '',
                    'category' => 'Activité',
                    'transmission' => '-',
                    'seats' => $activity->getCapacity() ?? 10,
                    'price' => (float) $price,
                    'original_price' => (float) $originalPrice,
                    'currency' => 'TND',
                    'provider' => 'Fly&Go',
                    'provider_logo' => '',
                    'image' => $activity->getImage() ? '/uploads/activities/' . $activity->getImage() : 'https://images.unsplash.com/photo-1530789253388-582c481c54b0?w=400',
                    'link' => '/activites/' . $activity->getId(),
                    'pickup_location' => $activity->getLieu() ?? '',
                    'fuel_policy' => '-',
                    'free_cancellation' => true,
                    'discount' => 0
                ];
            }

            if (empty($results)) {
                return ['success' => false, 'error' => 'Aucune activité disponible', 'results' => [], 'provider' => null];
            }

            return ['success' => true, 'results' => $results, 'provider' => 'Fly&Go', 'search_info' => []];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Erreur: ' . $e->getMessage(), 'results' => [], 'provider' => null];
        }
    }

    private function extractHotelAmenities($hotel): array
    {
        return ['WiFi', 'Parking'];
    }
}