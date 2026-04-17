<?php

namespace App\Service;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

class NearbyActivityService
{
    private $em;
    private $activityRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->activityRepo = $em->getRepository(Activity::class);
    }

    /**
     * getNearbyActivities with Haversine formula
     * @param float $lat
     * @param float $lng
     * @param float $maxDistance (default 20km)
     * @return array
     */
    public function getNearbyActivities(float $lat, float $lng, float $maxDistance = 20.0): array
    {
        $activities = $this->activityRepo->findBy(['actif' => true]);
        $nearby = [];

        foreach ($activities as $activity) {
            $activityLat = $activity->getLatitude();
            $activityLng = $activity->getLongitude();

            if ($activityLat === null || $activityLng === null) {
                continue;
            }

            $distance = $this->calculateDistance($lat, $lng, $activityLat, $activityLng);

            if ($distance <= $maxDistance) {
                $nearby[] = [
                    'activity' => $activity,
                    'distance' => round($distance, 2)
                ];
            }
        }

        // Sort by distance
        usort($nearby, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $nearby;
    }

    /**
     * Haversine formula
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
