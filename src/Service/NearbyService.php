<?php

namespace App\Service;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

class NearbyService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getNearbyActivities(float $lat, float $lng, float $radius = 10, int $limit = 10): array
    {
        $activities = $this->em->getRepository(Activity::class)->findBy(['actif' => true]);
        
        if (empty($activities)) {
            return [];
        }

        $results = [];
        
        foreach ($activities as $activity) {
            // For now, activities without coordinates get distance 0
            // In production, add lat/lng to Activity entity
            $distance = $this->calculateDistance($lat, $lng, $activity);
            
            if ($distance !== null && $distance <= $radius) {
                $results[] = [
                    'activity' => $activity,
                    'distance' => round($distance, 1)
                ];
            }
        }

        // Sort by distance
        usort($results, function($a, $b) {
            return $a['distance'] - $b['distance'];
        });

        return array_slice($results, 0, $limit);
    }

    private function calculateDistance(float $userLat, float $userLng, Activity $activity): ?float
    {
        // This is a simplified version
        // In production, activity should have lat/lng in the database
        // For demo, return random distance if activity lieu exists
        
        if (!$activity->getLieu()) {
            return null;
        }

        // For demo, we'll use dummy distances
        // In real implementation, add latitude/longitude columns to Activity
        return rand(1, 50) / 5; // Random distance 0.2 to 10 km
    }

    public function formatDistance(float $km): string
    {
        if ($km < 1) {
            return round($km * 1000) . ' m';
        }
        return round($km, 1) . ' km';
    }
}