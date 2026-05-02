<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    private $em;
    private $activityRepo;
    private $bookingRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->activityRepo = $em->getRepository(Activity::class);
        $this->bookingRepo = $em->getRepository(\App\Entity\Booking::class);
    }

    public function getRecommendedActivities(User $user, int $limit = 6): array
    {
        $activities = $this->activityRepo->findBy(['actif' => true]);
        
        if (empty($activities)) {
            return [];
        }

        // Get user's booking history
        $userBookings = $this->bookingRepo->findBy(['user' => $user]);
        
        // Calculate scores
        $scored = [];
        foreach ($activities as $activity) {
            $score = $this->calculateScore($activity, $userBookings);
            if ($score > 0) {
                $scored[] = [
                    'activity' => $activity,
                    'score' => $score
                ];
            }
        }

        // Sort by score descending
        usort($scored, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Return top results
        $results = array_slice($scored, 0, $limit);
        
        return array_map(function($item) {
            return $item['activity'];
        }, $results);
    }

    private function calculateScore(Activity $activity, array $userBookings): float
    {
        $score = 0;
        $userCategories = [];
        $userPlaces = [];
        $userPriceRange = null;

        // Analyze user's booking history
        foreach ($userBookings as $booking) {
            $activityBooked = $booking->getActivity();
            if ($activityBooked) {
                if ($activityBooked->getCategory()) {
                    $userCategories[] = $activityBooked->getCategory();
                }
                if ($activityBooked->getLieu()) {
                    $userPlaces[] = $activityBooked->getLieu();
                }
            }
        }

        // Category match
        if ($activity->getCategory() && in_array($activity->getCategory(), $userCategories)) {
            $score += 3;
        }

        // Location match
        if ($activity->getLieu() && in_array($activity->getLieu(), $userPlaces)) {
            $score += 2;
        }

        // Price similarity (activities in similar price range)
        $avgUserPrice = 0;
        if (!empty($userBookings)) {
            $total = 0;
            foreach ($userBookings as $booking) {
                $a = $booking->getActivity();
                if ($a) $total += $a->getPrice();
            }
            $avgUserPrice = $total / count($userBookings);
        }

        if ($avgUserPrice > 0) {
            $priceDiff = abs($activity->getPrice() - $avgUserPrice);
            if ($priceDiff < 50) {
                $score += 2;
            } elseif ($priceDiff < 100) {
                $score += 1;
            }
        }

        // Boost popular activities (check manually)
        $bookingCount = $activity->getBookings() ? $activity->getBookings()->count() : 0;
        if ($bookingCount > 5) {
            $score += 1;
        }
        if ($bookingCount > 10) {
            $score += 1;
        }

        // Random factor for variety
        $score += rand(0, 1);

        return $score;
    }

    public function getTrendingActivities(int $limit = 6): array
    {
        $activities = $this->activityRepo->findBy(['actif' => true]);
        
        if (empty($activities)) {
            return [];
        }
        
        // Sort by popularity (simple count)
        usort($activities, function($a, $b) {
            $countA = $a->getBookings() ? $a->getBookings()->count() : 0;
            $countB = $b->getBookings() ? $b->getBookings()->count() : 0;
            return $countB - $countA;
        });

        return array_slice($activities, 0, $limit);
    }
}