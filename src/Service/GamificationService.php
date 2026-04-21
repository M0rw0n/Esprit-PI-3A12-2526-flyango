<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

class GamificationService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkAndAwardBadge(User $user): ?array
    {
        // Count user's total bookings
        $bookings = $this->em->getRepository(Booking::class)->findBy(['user' => $user]);
        $count = count($bookings);

        // Define badge rules
        $badges = [
            [
                'name' => 'Explorateur',
                'description' => 'Première réservation!',
                'icon' => '🎉',
                'required' => 1,
                'earned' => false
            ],
            [
                'name' => 'Voyageur actif',
                'description' => '5 réservations effectuées!',
                'icon' => '🔥',
                'required' => 5,
                'earned' => false
            ],
            [
                'name' => 'Expert',
                'description' => '10+ réservations effectuées!',
                'icon' => '👑',
                'required' => 10,
                'earned' => false
            ],
            [
                'name' => 'Aventurier',
                'description' => '3 catégories différentes!',
                'icon' => '🌟',
                'required' => 3,
                'required_type' => 'categories',
                'earned' => false
            ]
        ];

        // Check categories
        $categories = [];
        foreach ($bookings as $booking) {
            $activity = $booking->getActivity();
            if ($activity && $activity->getCategory()) {
                $categories[] = $activity->getCategory();
            }
        }
        $uniqueCategories = count(array_unique($categories));

        // Check which badges to award
        $earnedBadges = [];
        foreach ($badges as &$badge) {
            // Check count-based badges
            if (!isset($badge['required_type'])) {
                if ($count >= $badge['required']) {
                    if (!$this->hasBadge($user, $badge['name'])) {
                        $this->awardBadge($user, $badge);
                        $earnedBadges[] = $badge;
                    }
                }
            }
            // Check category-based badges
            if (isset($badge['required_type']) && $badge['required_type'] === 'categories') {
                if ($uniqueCategories >= $badge['required']) {
                    if (!$this->hasBadge($user, $badge['name'])) {
                        $this->awardBadge($user, $badge);
                        $earnedBadges[] = $badge;
                    }
                }
            }
        }

        return $earnedBadges;
    }

    private function hasBadge(User $user, string $badgeName): bool
    {
        // In production, check database table
        // For now, return false to allow earning all badges
        return false;
    }

    private function awardBadge(User $user, array $badge): void
    {
        // In production, save to user_badges table
        // This is a placeholder
    }

    public function getUserBadges(User $user): array
    {
        // Return user's earned badges
        // In production, fetch from database
        return [
            ['name' => 'Explorateur', 'icon' => '🎉', 'earned' => true]
        ];
    }

    public function getBookingCount(User $user): int
    {
        return count($this->em->getRepository(Booking::class)->findBy(['user' => $user]));
    }
}