<?php

namespace App\Controller\Api;

use App\Repository\ActivityRepository;
use App\Service\Api\ActivityApiService;
use App\Service\Api\PlacesApiService;
use App\Service\Api\AiService;
use App\Service\Api\TripAdvisorService;
use App\Service\Api\ViatorService;
use App\Service\Api\GetYourGuideService;
use App\Service\RecommendationService;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ActivityApiController extends AbstractController
{
    private ?HttpClientInterface $client = null;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityApiService $activityService,
        private readonly PlacesApiService $placesService,
        private readonly AiService $aiService,
        private readonly TripAdvisorService $tripAdvisorService,
        private readonly ViatorService $viatorService,
        private readonly GetYourGuideService $getYourGuideService,
<<<<<<< HEAD
        private readonly \App\Service\NearbyActivityService $nearbyActivityService,
=======
>>>>>>> testsisi
        private ?RecommendationService $recommendationService = null,
    ) {}

    #[Route('/activities/nearby', name: 'api_activities_nearby', methods: ['GET'])]
    public function nearby(Request $request): JsonResponse
    {
<<<<<<< HEAD
        $lat = (float) $request->query->get('lat');
        $lng = (float) $request->query->get('lng');
        $maxDistance = (float) $request->query->get('distance', 20.0);

        if (!$lat || !$lng) {
            $lat = 36.8065;
            $lng = 10.1815;
        }

        $nearby = $this->nearbyActivityService->getNearbyActivities($lat, $lng, $maxDistance);
        $user = $this->getUser();
        
        // Get AI recommendations for scoring
        $recIds = [];
        if ($this->recommendationService) {
            try {
                $recommendations = $this->recommendationService->getAiRecommendations($user, $lat, $lng, 50);
                $recIds = array_map(fn($a) => $a->getId(), $recommendations);
            } catch (\Exception $e) {
                // Ignore AI errors and continue with normal results
            }
        }

        $formatted = [];
        foreach ($nearby as $item) {
            $activity = $item['activity'];
            $isRecommended = in_array($activity->getId(), $recIds);
            
            $formatted[] = [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'price' => $activity->getPrice(),
                'rating' => $activity->getRating() ?? ($activity->getNoteMoyenne() ?: 4.5),
                'distance' => $item['distance'],
                'lat' => $activity->getLatitude(),
                'lng' => $activity->getLongitude(),
                'image' => $activity->getImage(),
                'category' => $activity->getCategory(),
                'description' => substr($activity->getDescription() ?? '', 0, 100) . '...',
                'lieu' => $activity->getLieu(),
                'isRecommended' => $isRecommended,
                'isClose' => $item['distance'] <= 5
            ];
        }

        return new JsonResponse([
            'success' => true,
            'results' => $formatted,
            'center' => ['lat' => $lat, 'lng' => $lng]
        ]);
=======
        $lat = (float) $request->query->get('lat', 0);
        $lng = (float) $request->query->get('lng', 0);
        $category = $request->query->get('category', '');
        $radius = (int) $request->query->get('radius', 5000);

        if ($lat === 0 && $lng === 0) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Coordonnées requis (lat, lng)'
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->activityService->getNearbyActivities($lat, $lng, $category, $radius);

        return new JsonResponse($result);
>>>>>>> testsisi
    }

    #[Route('/activities/search', name: 'api_activities_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return new JsonResponse(['success' => false, 'error' => 'Query too short']);
        }

        $queryLower = strtolower($query);
        $queryWords = array_filter(
            preg_replace('/[^a-zA-Z]/', ' ', $queryLower),
            fn($w) => strlen(trim($w)) > 1
        );
        
        $stopWords = ['je', 'veux', 'visiter', 'aller', 'une', 'des', 'avec', 'pour', 'mon', 'mes', 'le', 'la', 'les', 'un', 'voir', 'decouvrir'];
        $searchWords = array_filter($queryWords, fn($w) => !in_array($w, $stopWords));
        
        $activities = $this->activityRepository->findAll();
        
        $matched = [];
        foreach ($activities as $activity) {
            $activityText = strtolower($this->getActivityText($activity));
            
            $score = 0;
            $foundWords = [];
            
            foreach ($searchWords as $word) {
                if (strlen($word) < 2) continue;
                
                if (strpos($activityText, $word) !== false) {
                    $score += 1;
                    $foundWords[] = $word;
                }
            }
            
            if ($score > 0) {
                $matched[] = [
                    'activity' => $activity,
                    'score' => $score,
                    'matchedKeywords' => array_unique($foundWords)
                ];
            }
        }
        
        usort($matched, fn($a, $b) => $b['score'] <=> $a['score']);
        
        $formatted = [];
        foreach ($matched as $item) {
            $activity = $item['activity'];
            $formatted[] = [
                'id' => $activity->getId(),
                'title' => method_exists($activity, 'getTitle') ? $activity->getTitle() : 'Activité',
                'lieu' => method_exists($activity, 'getLieu') ? $activity->getLieu() : '',
                'description' => method_exists($activity, 'getDescription') ? $activity->getDescription() : '',
                'price' => method_exists($activity, 'getPrice') ? $activity->getPrice() : null,
                'image' => method_exists($activity, 'getImage') ? $activity->getImage() : null,
            ];
        }
        
        return new JsonResponse([
            'success' => true,
            'results' => $formatted,
            'query' => $query,
            'count' => count($formatted)
        ]);
    }
    
    private function getActivityText($activity): string
    {
        $parts = [];
        if (method_exists($activity, 'getTitle')) $parts[] = $activity->getTitle() ?? '';
        if (method_exists($activity, 'getDescription')) $parts[] = $activity->getDescription() ?? '';
        if (method_exists($activity, 'getLieu')) $parts[] = $activity->getLieu() ?? '';
        if (method_exists($activity, 'getCategory')) $parts[] = $activity->getCategory() ?? '';
        return implode(' ', $parts);
    }

    #[Route('/activities/recommendations', name: 'api_activities_recommendations', methods: ['GET'])]
    public function recommendations(Request $request): JsonResponse
    {
        try {
            $location = $request->query->get('location', '');
            $interests = $request->query->get('interests', '');
            $interestsArray = $interests ? explode(',', $interests) : [];

            if (empty($location)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Paramètre "location" requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Normalize location for matching
            $locationLower = strtolower(trim($location));
            
            // Location variants mapping
            $locationMap = [
                'djerba' => ['djerba', 'zarzis', 'midoun', 'houmt souk', 'mellita'],
                'tunis' => ['tunis', 'carthage', 'sidi bou said', 'la marsa', 'gomam'],
                'hammamet' => ['hammamet', 'nabeul', 'yassine', 'sidi thabet'],
                'sousse' => ['sousse', 'monastir', 'mahdia', 'kairouan'],
                'douz' => ['douz', 'kebili', 'sand', 'dunes', 'sahara'],
                'matmata' => ['matmata', 'tataouine', 'chenini', 'ksar'],
                'tozeur' => ['tozeur', 'nefta', 'degache', 'oasis'],
                'sfax' => ['sfax', 'kairouan'],
                'kairouan' => ['kairouan', 'sbeïtla'],
            ];
            
            $searchTerms = $locationMap[$locationLower] ?? [$locationLower];

            // STEP 1: STRICT FILTER - Get only activities from this location
            $allActivities = $this->activityRepository->findBy(['actif' => true]);
            
            // Filter activities where lieu contains the search terms
            $filteredActivities = array_filter($allActivities, function($activity) use ($searchTerms) {
                $activityLieu = method_exists($activity, 'getLieu') ? strtolower($activity->getLieu() ?? '') : '';
                $activityTitle = method_exists($activity, 'getTitle') ? strtolower($activity->getTitle() ?? '') : '';
                
                foreach ($searchTerms as $term) {
                    if (!empty($activityLieu) && (stripos($activityLieu, $term) !== false || stripos($term, $activityLieu) !== false)) {
                        return true;
                    }
                    if (!empty($activityTitle) && (stripos($activityTitle, $term) !== false)) {
                        return true;
                    }
                }
                return false;
            });

            // STEP 2: FALLBACK - If no activities in this location, get top rated overall
            if (empty($filteredActivities)) {
                // Sort by rating and get top 6
                usort($allActivities, function($a, $b) {
                    $ratingA = method_exists($a, 'getNoteMoyenne') ? ($a->getNoteMoyenne() ?? 0) : 0;
                    $ratingB = method_exists($b, 'getNoteMoyenne') ? ($b->getNoteMoyenne() ?? 0) : 0;
                    return $ratingB - $ratingA;
                });
                $filteredActivities = array_slice($allActivities, 0, 6);
            }

            // STEP 3: Calculate scores
            $scored = [];
            foreach ($filteredActivities as $activity) {
                $score = 0;
                $rating = 0;
                
                // Get activity details
                $activityTitle = method_exists($activity, 'getTitle') ? $activity->getTitle() : '';
                $activityCategory = method_exists($activity, 'getCategory') ? strtolower($activity->getCategory() ?? '') : '';
                $activityLieu = method_exists($activity, 'getLieu') ? $activity->getLieu() : '';
                
                // 1. LOCATION EXACT MATCH (+4 pts)
                $activityLieuLower = strtolower($activityLieu ?? '');
                foreach ($searchTerms as $term) {
                    if (stripos($activityLieuLower, $term) !== false) {
                        $score += 4;
                        break;
                    }
                }
                
                // 2. CATEGORY MATCH (+3 pts)
                $categoryKeywords = [
                    'plage' => ['plage', 'mer', 'nautique', 'banane', 'pêche'],
                    'désert' => ['desert', 'safari', 'quad', 'chet', 'dune', 'sahara'],
                    'culture' => ['culture', 'musée', 'histoire', 'patrimoine', 'monument', 'visite'],
                    'aventure' => ['aventure', 'randonnee', 'trek', 'escalade'],
                    'bien_être' => ['spa', 'bien-être', 'detente', 'relax', 'massage'],
                    'gastronomie' => ['gastronomie', 'cuisine', 'restaurant', 'food', 'diner'],
                ];
                
                foreach ($searchTerms as $term) {
                    foreach ($categoryKeywords as $cat => $keywords) {
                        if (stripos($term, $cat) !== false) {
                            foreach ($keywords as $kw) {
                                if (stripos($activityCategory, $kw) !== false) {
                                    $score += 3;
                                    break 3;
                                }
                            }
                        }
                    }
                }
                
                // 3. USER INTERESTS (+2 pts)
                foreach ($interestsArray as $interest) {
                    $interestLower = strtolower(trim($interest));
                    if (stripos($activityCategory, $interestLower) !== false) {
                        $score += 2;
                    }
                }
                
                // 4. RATING BOOST (+3 pts max)
                try {
                    $rating = method_exists($activity, 'getNoteMoyenne') ? ($activity->getNoteMoyenne() ?? 0) : 0;
                } catch (\Exception $e) {
                    $rating = 0;
                }
                if ($rating >= 4.5) $score += 3;
                elseif ($rating >= 4) $score += 2;
                elseif ($rating >= 3) $score += 1;
                
                // 5. POPULARITY (+2 pts max)
                $bookingCount = 0;
                try {
                    $bookings = method_exists($activity, 'getBookings') ? $activity->getBookings() : null;
                    $bookingCount = $bookings ? $bookings->count() : 0;
                } catch (\Exception $e) {
                    $bookingCount = 0;
                }
                if ($bookingCount >= 10) $score += 2;
                elseif ($bookingCount >= 5) $score += 1;
                
                // 6. Image bonus (+1 pt)
                $hasImage = method_exists($activity, 'getImage') && !empty($activity->getImage());
                if ($hasImage) $score += 1;
                
                $scored[] = [
                    'activity' => $activity,
                    'score' => $score,
                    'rating' => $rating
                ];
            }
            
            // Sort by score
            usort($scored, fn($a, $b) => $b['score'] - $a['score']);
            
            // Return top 6
            $results = array_slice($scored, 0, 6);
            
<<<<<<< HEAD
            // AI REINFORCEMENT
            $aiSuggestions = "";
            try {
                $prompt = "Recommande 3 activités insolites à $location liées à " . ($interests ?: "la détente et l'aventure") . ". Sois bref.";
                $aiResponse = $this->aiService->generateResponse($prompt);
                $aiSuggestions = $aiResponse['response'] ?? "";
            } catch (\Exception $e) {
                $aiSuggestions = "Découvrez les merveilles de $location avec nos guides locaux.";
            }

            return new JsonResponse([
                'success' => true,
                'location' => $location,
                'ai_suggestions' => $aiSuggestions,
                'recommendations' => array_map(fn($item) => [
                    'id' => $item['activity']->getId(),
                    'title' => method_exists($item['activity'], 'getTitle') ? $item['activity']->getTitle() : 'Activité',
                    'lieu' => method_exists($item['activity'], 'getLieu') ? $item['activity']->getLieu() : '',
                    'description' => method_exists($item['activity'], 'getDescription') ? $item['activity']->getDescription() : '',
                    'price' => method_exists($item['activity'], 'getPrice') ? $item['activity']->getPrice() : null,
                    'rating' => $item['rating'],
                    'image' => method_exists($item['activity'], 'getImage') ? $item['activity']->getImage() : null,
=======
            // If still empty, show message
            if (empty($results)) {
                return new JsonResponse([
                    'success' => true,
                    'location' => $location,
                    'recommendations' => [],
                    'message' => 'Aucune activité trouvée pour ' . $location
                ]);
            }
            
            return new JsonResponse([
                'success' => true,
                'location' => $location,
                'recommendations' => array_map(fn($item) => [
                    'id' => $item['activity']->getId(),
                    'title' => method_exists($item['activity'], 'getTitle') ? $item['activity']->getTitle() : 'Activité',
                    'description' => method_exists($item['activity'], 'getDescription') ? substr($item['activity']->getDescription() ?? '', 0, 150) : '',
                    'price' => method_exists($item['activity'], 'getPrice') ? $item['activity']->getPrice() : 0,
                    'image' => method_exists($item['activity'], 'getImage') ? $item['activity']->getImage() : '',
                    'category' => method_exists($item['activity'], 'getCategory') ? $item['activity']->getCategory() : '',
                    'lieu' => method_exists($item['activity'], 'getLieu') ? $item['activity']->getLieu() : '',
                    'rating' => $item['rating'],
>>>>>>> testsisi
                    'score' => $item['score']
                ], $results)
            ]);
        } catch (\Exception $e) {
<<<<<<< HEAD
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
=======
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], 500);
>>>>>>> testsisi
        }
    }

    #[Route('/activities/{id}/reviews', name: 'api_activities_reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): JsonResponse
    {
        $locationId = $request->query->get('location_id', '');
        
        if ($locationId) {
            $reviews = $this->tripAdvisorService->getReviews($locationId);
        } else {
            $reviews = $this->getMockReviews($id);
        }
        
        return new JsonResponse([
            'success' => true,
            'activity_id' => $id,
            'reviews' => $reviews
        ]);
    }

    private function getMockReviews(int $activityId): array
    {
        return [
            [
                'author' => 'Marie D.',
                'rating' => 5,
                'date' => date('Y-m-d', strtotime('-5 days')),
                'title' => 'Excellent expérience!',
                'text' => 'Une activité vraiment passionnante. Le guide était très professionnel et nous a fait découvrir des endroits incroyables.'
            ],
            [
                'author' => 'Pierre L.',
                'rating' => 4,
                'date' => date('Y-m-d', strtotime('-10 days')),
                'title' => 'Très bonne activité',
                'text' => 'Bien organisé, ponctuel et instructif. Je recommande cette activité à tous les visiteurs.'
            ],
            [
                'author' => 'Sophie M.',
                'rating' => 5,
                'date' => date('Y-m-d', strtotime('-15 days')),
                'title' => 'Incontournable!',
                'text' => 'Le highlight de notre voyage. À ne pas manquer!'
            ]
        ];
    }

    #[Route('/activities/{id}/images', name: 'api_activities_images', methods: ['GET'])]
    public function images(int $id, Request $request): JsonResponse
    {
        $name = $request->query->get('name', 'activity');
        $source = $request->query->get('source', 'unsplash');

        $images = $this->activityService->getActivityImages($name);

        if (empty($images['images']) && $source === 'pexels') {
            $images = $this->getPexelsImages($name);
        }

        return new JsonResponse($images);
    }

    #[Route('/activities/{id}/book', name: 'api_activities_book', methods: ['POST'])]

    #[Route('/activities/{id}/book', name: 'api_activities_book', methods: ['POST'])]
    public function book(int $id, Request $request): JsonResponse
    {
        $provider = $request->request->get('provider', 'viator');
        $date = $request->request->get('date', date('Y-m-d'));
        $persons = (int) $request->request->get('persons', 1);
        $tourId = (int) $request->request->get('tour_id', 0);

        if ($provider === 'viator') {
            $productCode = $request->request->get('product_code', 'VIATOR-MOCK-' . $id);
            $result = $this->viatorService->bookProduct($productCode, $date, $persons);
        } elseif ($provider === 'getyourguide') {
            $tourId = $tourId ?: $id;
            $result = $this->getYourGuideService->bookTour($tourId, $date, $persons);
        } else {
            return new JsonResponse([
                'success' => false,
                'error' => 'Provider non supporté'
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result);
    }

    #[Route('/activities/recommend-ai', name: 'api_activities_recommend_ai', methods: ['POST'])]
    public function recommendWithAi(Request $request): JsonResponse
    {
        $location = $request->request->get('location', '');
        $preferences = $request->request->get('preferences', '');

        if (empty($location)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Paramètre "location" requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        $prompt = "En tant qu'expert en voyage en Tunisie, recommande 5 activités uniques à $location pour un voyageur interested in: $preferences. Pour chaque activité, fournis: nom, description courte, type d'activité, et durée estimée.";
        
        $result = $this->aiService->generateResponse($prompt);

        return new JsonResponse([
            'success' => $result['success'],
            'recommendations' => $result['response'],
            'model' => $result['model'] ?? 'unknown'
        ]);
    }

    private function getPexelsImages(string $query): array
    {
        try {
            $response = $this->getClient()->request('GET', 'https://api.pexels.com/v1/search', [
                'query' => [
                    'query' => $query,
                    'per_page' => 4
                ],
                'headers' => [
                    'Authorization' => $_ENV['PEXELS_API_KEY'] ?? ''
                ]
            ]);

            $data = $response->toArray();
            $images = [];

            foreach ($data['photos'] ?? [] as $photo) {
                $images[] = [
                    'regular' => $photo['src']['large'] ?? '',
                    'thumb' => $photo['src']['medium'] ?? '',
                    'alt' => $photo['alt'] ?? '',
                    'author' => $photo['photographer'] ?? ''
                ];
            }

            return ['success' => true, 'source' => 'pexels', 'images' => $images];
        } catch (\Exception $e) {
            return ['success' => false, 'source' => 'pexels', 'images' => []];
        }
    }

    private function getClient(): HttpClientInterface
    {
        if (!isset($this->client)) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }
}