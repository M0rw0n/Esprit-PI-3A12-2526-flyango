<?php

namespace App\Service;

class GamificationService
{
    private array $badges = [
        'first_booking' => [
            'name' => 'Premier Voyageur',
            'icon' => 'fa-star',
            'color' => '#F59E0B',
            'description' => 'Première réservation',
            'points' => 50
        ],
        'explorer' => [
            'name' => 'Explorateur',
            'icon' => 'fa-compass',
            'color' => '#10B981',
            'description' => '3 hébergements différents',
            'points' => 100
        ],
        'loyal' => [
            'name' => 'Fidèle',
            'icon' => 'fa-heart',
            'color' => '#EF4444',
            'description' => '3 réservations',
            'points' => 150
        ],
        'reviewer' => [
            'name' => 'Critique',
            'icon' => 'fa-pen',
            'color' => '#8B5CF6',
            'description' => '5 avis rédigés',
            'points' => 75
        ],
        'social' => [
            'name' => 'Social',
            'icon' => 'fa-users',
            'color' => '#06B6D4',
            'description' => 'Ajouté aux favoris',
            'points' => 25
        ],
        'vip' => [
            'name' => 'VIP',
            'icon' => 'fa-crown',
            'color' => '#F59E0B',
            'description' => '500+ points累计',
            'points' => 0
        ],
        'super_vip' => [
            'name' => 'Super VIP',
            'icon' => 'fa-gem',
            'color' => '#EC4899',
            'description' => '1000+ points累计',
            'points' => 0
        ],
        'night_owl' => [
            'name' => 'Oiseau de Nuit',
            'icon' => 'fa-moon',
            'color' => '#6366F1',
            'description' => '10+ nuits réservées',
            'points' => 100
        ],
        'beach_lover' => [
            'name' => 'Amant de la Plage',
            'icon' => 'fa-umbrella-beach',
            'color' => '#14B8A6',
            'description' => 'Hébergement plage',
            'points' => 50
        ],
        'adventurer' => [
            'name' => 'Aventurier',
            'icon' => 'fa-hiking',
            'color' => '#F97316',
            'description' => '3 régions différentes',
            'points' => 100
        ]
    ];

    private array $levels = [
        ['name' => 'Novice', 'min' => 0, 'icon' => 'fa-seedling'],
        ['name' => 'Voyageur', 'min' => 100, 'icon' => 'fa-walking'],
        ['name' => 'Explorateur', 'min' => 300, 'icon' => 'fa-compass'],
        ['name' => 'Aventurier', 'min' => 600, 'icon' => 'fa-mountain'],
        ['name' => 'Guide', 'min' => 1000, 'icon' => 'fa-map'],
        ['name' => 'Expert', 'min' => 2000, 'icon' => 'fa-globe-africa'],
        ['name' => 'Maître', 'min' => 5000, 'icon' => 'fa-crown'],
        ['name' => 'Légende', 'min' => 10000, 'icon' => 'fa-gem']
    ];

    public function getBadges(): array
    {
        return $this->badges;
    }

    public function getBadge(string $key): ?array
    {
        return $this->badges[$key] ?? null;
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    public function getLevel(int $points): array
    {
        $currentLevel = $this->levels[0];
        $nextLevel = null;
        
        foreach ($this->levels as $level) {
            if ($points >= $level['min']) {
                $currentLevel = $level;
                $nextLevel = $level;
            }
        }
        
        $levelIndex = array_search($currentLevel, $this->levels);
        if ($levelIndex < count($this->levels) - 1) {
            $nextLevel = $this->levels[$levelIndex + 1];
        }
        
        $progress = 0;
        if ($nextLevel && $nextLevel['min'] > $currentLevel['min']) {
            $progress = (($points - $currentLevel['min']) / ($nextLevel['min'] - $currentLevel['min'])) * 100;
        }
        
        return [
            'current' => $currentLevel,
            'next' => $nextLevel,
            'progress' => min(100, round($progress)),
            'points_to_next' => $nextLevel ? max(0, $nextLevel['min'] - $points) : 0
        ];
    }

    public function calculatePoints(string $action, array $context = []): int
    {
        return match($action) {
            'booking' => $context['nights'] ?? 1 * 10,
            'first_booking' => 50,
            'review' => 25,
            'first_review' => 50,
            'add_favorite' => 10,
            'share' => 15,
            'view' => 1,
            default => 0
        };
    }

    public function getPointsForBadge(string $badgeKey): int
    {
        return $this->badges[$badgeKey]['points'] ?? 0;
    }

    public function shouldUnlockBadge(string $badgeKey, array $userStats): bool
    {
        return match($badgeKey) {
            'first_booking' => ($userStats['bookings'] ?? 0) >= 1,
            'explorer' => ($userStats['unique_hebergements'] ?? 0) >= 3,
            'loyal' => ($userStats['bookings'] ?? 0) >= 3,
            'reviewer' => ($userStats['reviews'] ?? 0) >= 5,
            'social' => ($userStats['favorites'] ?? 0) >= 1,
            'vip' => ($userStats['total_points'] ?? 0) >= 500,
            'super_vip' => ($userStats['total_points'] ?? 0) >= 1000,
            'night_owl' => ($userStats['total_nights'] ?? 0) >= 10,
            'beach_lover' => ($userStats['beach_stays'] ?? 0) >= 1,
            'adventurer' => ($userStats['unique_regions'] ?? 0) >= 3,
            default => false
        };
    }

    public function getLeaderboardPosition(int $userPoints, array $allPoints): int
    {
        $position = 1;
        foreach ($allPoints as $points) {
            if ($points > $userPoints) {
                $position++;
            }
        }
        return $position;
    }
}