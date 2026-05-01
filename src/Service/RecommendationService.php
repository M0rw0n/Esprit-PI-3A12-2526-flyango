<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    private $em;
    private $activityRepo;
    private $bookingRepo;
    private $nearbyService;

    public function __construct(EntityManagerInterface $em, NearbyActivityService $nearbyService)
    {
        $this->em = $em;
        $this->activityRepo = $em->getRepository(Activity::class);
        $this->bookingRepo = $em->getRepository(\App\Entity\Booking::class);
        $this->nearbyService = $nearbyService;
    }

    /**
     * Get AI recommendations based on user history and location
     */
    public function getAiRecommendations(?User $user, ?float $lat = null, ?float $lng = null, int $limit = 5): array
    {
        $activities = $this->activityRepo->findBy(['actif' => true]);
        if (empty($activities)) return [];

        $userBookings = $user ? $this->bookingRepo->findBy(['user' => $user]) : [];
        $trending = $this->getTrendingActivities(20);
        $trendingIds = array_map(fn($a) => $a->getId(), $trending);

        $scored = [];
        foreach ($activities as $activity) {
            $score = $this->calculateAiScore($activity, $user, $userBookings, $trendingIds, $lat, $lng);
            $scored[] = [
                'activity' => $activity,
                'score' => $score
            ];
        }

        // Sort by score descending
        usort($scored, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $results = array_slice($scored, 0, $limit);
        
        // Fallback if results are too few or low score
        if (empty($results)) {
            return array_slice($trending, 0, $limit);
        }

        return array_map(fn($item) => $item['activity'], $results);
    }

    /**
     * AI Scoring Logic:
     * 35% Proximity (if location provided)
     * 25% Category match (user history)
     * 20% Popularity (rating/bookings)
     * 10% Price match (user average)
     * 10% Trend (trending list)
     */
    private function calculateAiScore(Activity $activity, ?User $user, array $userBookings, array $trendingIds, ?float $lat, ?float $lng): float
    {
        $score = 0;

        // 1. Proximity (35%)
        if ($lat !== null && $lng !== null && $activity->getLatitude() !== null && $activity->getLongitude() !== null) {
            $dist = $this->calculateDistance($lat, $lng, $activity->getLatitude(), $activity->getLongitude());
            if ($dist <= 5) $score += 0.35;
            elseif ($dist <= 15) $score += 0.25;
            elseif ($dist <= 30) $score += 0.15;
            elseif ($dist <= 50) $score += 0.05;
        }

        // 2. Category match (25%)
        if ($user) {
            $userCategories = [];
            foreach ($userBookings as $booking) {
                if ($booking->getActivity() && $booking->getActivity()->getCategory()) {
                    $userCategories[] = $booking->getActivity()->getCategory();
                }
            }
            if ($activity->getCategory() && in_array($activity->getCategory(), $userCategories)) {
                $score += 0.25;
            }
        } else {
            // Fallback for non-logged users: diversity score or category popularity
            $score += 0.10; 
        }

        // 3. Popularity/Rating (20%)
        $rating = $activity->getRating() ?? ($activity->getNoteMoyenne() ?: 4.0);
        $bookingCount = $activity->getBookings() ? $activity->getBookings()->count() : 0;
        
        $popScore = (($rating / 5) * 0.15) + (min($bookingCount, 50) / 50 * 0.05);
        $score += $popScore;

        // 4. Price match (10%)
        if ($user && !empty($userBookings)) {
            $total = 0;
            $count = 0;
            foreach ($userBookings as $b) {
                if ($b->getActivity()) {
                    $total += $b->getActivity()->getPrice();
                    $count++;
                }
            }
            $avgPrice = $count > 0 ? $total / $count : 0;

            if ($avgPrice > 0) {
                $priceDiff = abs($activity->getPrice() - $avgPrice);
                if ($priceDiff < 20) $score += 0.10;
                elseif ($priceDiff < 50) $score += 0.05;
            }
        } else {
            // Fallback: reward activities with "good" price (not too expensive)
            if ($activity->getPrice() < 100) $score += 0.08;
            elseif ($activity->getPrice() < 200) $score += 0.04;
        }

        // 5. Trend (10%)
        if (in_array($activity->getId(), $trendingIds)) {
            $score += 0.10;
        }

        return $score;
    }

    public function getTrendingActivities(int $limit = 6): array
    {
        $activities = $this->activityRepo->findBy(['actif' => true]);
        
        if (empty($activities)) {
            return [];
        }
        
        usort($activities, function($a, $b) {
            $countA = $a->getBookings() ? $a->getBookings()->count() : 0;
            $countB = $b->getBookings() ? $b->getBookings()->count() : 0;
            return $countB - $countA;
        });

        return array_slice($activities, 0, $limit);
    }

    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
