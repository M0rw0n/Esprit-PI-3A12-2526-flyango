<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class GeocodingService
{
    private string $apiKey;
    private ?RequestStack $requestStack;

    public function __construct(
        string $geoapifyApiKey = '',
        RequestStack $requestStack = null
    ) {
        $this->apiKey = $geoapifyApiKey;
        $this->requestStack = $requestStack;
    }

    public function getCoordinates(string $address): ?array
    {
        // Using OpenStreetMap (free, no key needed)
        $address = urlencode($address . ', Tunisia');
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=$address&limit=1";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header' => "User-Agent: FlyGoApp/1.0 (contact@flyandgo.tn)\r\n"
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                    'display_name' => $data[0]['display_name'] ?? ''
                ];
            }
        }
        
        // Fallback: try without Tunisia
        $address2 = urlencode($address);
        $response2 = @file_get_contents("https://nominatim.openstreetmap.org/search?format=json&q=$address2&limit=1", false, $context);
        
        if ($response2) {
            $data2 = json_decode($response2, true);
            if (!empty($data2) && isset($data2[0]['lat']) && isset($data2[0]['lon'])) {
                return [
                    'latitude' => (float) $data2[0]['lat'],
                    'longitude' => (float) $data2[0]['lon'],
                    'display_name' => $data2[0]['display_name'] ?? ''
                ];
            }
        }
        
        return null;
    }

    public function getCoordinatesWithKey(string $address): ?array
    {
        // If API key is provided, use Geoapify
        if ($this->apiKey) {
            $address = urlencode($address);
            $url = "https://api.geoapify.com/v1/geocode/search?text=$address&filter=countrycode:tn&format=json&apiKey=" . $this->apiKey;
            
            $response = @file_get_contents($url);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['results'])) {
                    return [
                        'latitude' => (float) $data['results'][0]['lat'],
                        'longitude' => (float) $data['results'][0]['lon'],
                        'display_name' => $data['results'][0]['formatted'] ?? ''
                    ];
                }
            }
        }
        
        return $this->getCoordinates($address);
    }
}