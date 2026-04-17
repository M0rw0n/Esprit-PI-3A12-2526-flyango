<?php

namespace App\Controller;

use App\Service\Api\YelpService;
use App\Service\Api\OpenTripMapService;
use App\Service\Api\SportsDataService;
use App\Service\Api\GeoNamesService;
use App\Service\Api\UnsplashService;
use App\Service\Api\PlacesApiService;
use App\Service\Api\GoogleSearchService;
use App\Service\Api\WikipediaService;
use App\Entity\Activity;
use App\Entity\Booking;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ActivityRepository;
use App\Repository\BookingRepository;
use App\Service\Api\PaymentService;
use App\Service\SentimentService;
use App\Service\Api\MailerService;
use App\Service\ReceiptService;
use App\Service\RecommendationService;
use App\Service\ActivityShareService;
use App\Service\GamificationService;
use App\Service\ContentModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activites')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ReceiptService $receiptService,
        private readonly YelpService $yelpService,
        private readonly OpenTripMapService $otmService,
        private readonly SportsDataService $sportsService,
        private readonly GeoNamesService $geoNamesService,
        private readonly UnsplashService $unsplashService,
        private readonly PlacesApiService $placesService,
        private readonly GoogleSearchService $googleSearchService,
        private readonly WikipediaService $wikipediaService,
        private readonly ?MailerService $mailerService = null,
        private readonly ?SentimentService $sentimentService = null,
        private ?EntityManagerInterface $em = null,
        private ?ContentModerationService $moderationService = null,
    ) {
    }

    #[Route('/decouverte-reelle', name: 'activity_discovery', methods: ['GET'])]
    public function discovery(Request $request): Response
    {
        $lieu = $request->query->get('lieu', 'Tunis');
        $type = $request->query->get('type', 'all');

        $results = [
            'restaurants' => [],
            'attractions' => [],
            'hotels' => [],
            'city_info' => [],
            'search_results' => []
        ];

        // Get city info from Wikipedia
        $cityInfo = $this->wikipediaService->getCityInfo($lieu);
        $results['city_info'] = $cityInfo;

        // Get restaurants
        if ($type === 'all' || $type === 'restaurants') {
            $results['restaurants'] = $this->wikipediaService->searchRestaurants($lieu, 'fr', 12);
            if (empty($results['restaurants'])) {
                $results['restaurants'] = $this->wikipediaService->searchRestaurants($lieu, 'en', 12);
            }
        }

        // Get attractions/tourist places
        if ($type === 'all' || $type === 'culture') {
            $results['attractions'] = $this->wikipediaService->searchAttractions($lieu, 'fr', 12);
            if (empty($results['attractions'])) {
                $results['attractions'] = $this->wikipediaService->searchAttractions($lieu, 'en', 12);
            }
        }

        // Get hotels
        if ($type === 'all') {
            $results['hotels'] = $this->wikipediaService->searchHotels($lieu, 'fr', 8);
            if (empty($results['hotels'])) {
                $results['hotels'] = $this->wikipediaService->searchHotels($lieu, 'en', 8);
            }
        }

        // Fallback to Yelp if available
        if ($type === 'all' || $type === 'restaurants') {
            $yelpData = $this->yelpService->searchBusinesses('restaurants', $lieu, 6);
            if (!empty($yelpData['businesses'])) {
                $results['restaurants'] = array_merge(
                    array_map(function($r) {
                        return [
                            'title' => $r['name'] ?? '',
                            'snippet' => $r['categories'][0]['title'] ?? 'Restaurant',
                            'image_url' => $r['image_url'] ?? $this->unsplashService->getRandomImage($r['name'] ?? '', 'restaurant'),
                            'rating' => $r['rating'] ?? 0,
                            'price' => $r['price'] ?? '$$'
                        ];
                    }, $yelpData['businesses']),
                    $results['restaurants']
                );
            }
        }

        return $this->render('activity/discovery.html.twig', [
            'results' => $results,
            'lieu' => $lieu,
            'type' => $type
        ]);
    }
    #[Route('', name: 'activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $repo, PaginatorInterface $paginator): Response
    {
        $q    = $request->query->get('q');
        $lieu = $request->query->get('lieu');
        $categorie = $request->query->get('categorie');
        $prixMax = $request->query->get('prix_max') ? (float) $request->query->get('prix_max') : null;
        $tri = $request->query->get('tri');

        $qb = $repo->createQueryBuilder('a');
        
        switch ($tri) {
            case 'price_asc':
                $qb->orderBy('a.price', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('a.price', 'DESC');
                break;
            case 'name':
                $qb->orderBy('a.title', 'ASC');
                break;
            default:
                $qb->orderBy('a.id', 'DESC');
                break;
        }
        
        if ($q) {
            $qb->andWhere('a.title LIKE :q OR a.description LIKE :q')->setParameter('q', '%' . $q . '%');
        }
        if ($lieu) {
            $qb->andWhere('a.lieu = :lieu')->setParameter('lieu', $lieu);
        }
        if ($categorie) {
            $qb->andWhere('a.category = :cat')->setParameter('cat', $categorie);
        }
        if ($prixMax) {
            $qb->andWhere('a.price <= :prixMax')->setParameter('prixMax', $prixMax);
        }

        $activities = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 8);

        if ($request->isXmlHttpRequest()) {
            $data = array_map(fn(Activity $a) => [
                'id'          => $a->getId(),
                'title'       => $a->getTitle(),
                'description' => $a->getDescription(),
                'price'       => $a->getPrice(),
                'duration'    => $a->getDuration(),
                'lieu'        => $a->getLieu(),
                'image'       => $a->getImage(),
            ], $activities->getItems());
            return new JsonResponse(['activities' => $data]);
        }

        return $this->render('activity/index.html.twig', [
            'activities' => $activities,
            'q'          => $q,
            'lieu'       => $lieu,
            'categorie'  => $categorie,
            'prixMax'    => $request->query->get('prix_max'),
            'tri'        => $tri,
        ]);
    }

    #[Route('/autour-de-moi', name: 'activity_nearby_map', methods: ['GET'])]
    public function nearbyMap(): Response
    {
        return $this->render('activity/nearby.html.twig');
    }

    #[Route('/{id}', name: 'activity_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(int $id, ActivityRepository $repo): Response
    {
        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();
        return $this->render('activity/show.html.twig', ['activity' => $a]);
    }

    #[Route('/{id}/reserver', name: 'activity_book', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function book(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();
        $persons = (int)$request->request->get('persons', 1);
        $bookingDate = $request->request->get('booking_date');
        $paymentMethod = $request->request->get('payment_method', 'stripe');

        $totalPrice = $persons * $a->getPrice();
        $serviceName = $a->getTitle();
        $serviceDate = $bookingDate ?: date('Y-m-d');

        $bookingData = [
            'type' => 'ACTIVITY',
            'service_name' => $serviceName,
            'service_date' => $serviceDate,
            'total_amount' => $totalPrice,
            'unit_price' => $a->getPrice(),
            'quantity' => $persons,
            'customer_name' => $user->getFullName(),
            'customer_email' => $user->getEmail(),
            'customer_phone' => $user->getTelephone() ?? '',
            'payment_method' => $paymentMethod,
            'activity_id' => $a->getId(),
            'user_id' => $user->getId(),
        ];

        $result = $this->processPayment($bookingData, $paymentMethod, $user);

        if (!$result['success']) {
            $this->addFlash('error', 'Paiement échoué: ' . ($result['error'] ?? 'Erreur inconnue'));
            return $this->redirectToRoute('activity_show', ['id' => $id]);
        }

        $booking = new Booking();
        $booking->setActivity($a)
                ->setUser($user)
                ->setCustomerName($user->getFullName())
                ->setEmail($user->getEmail())
                ->setClientPhone($user->getTelephone())
                ->setPersons($persons)
                ->setTotalPrice($totalPrice)
                ->setStatus('PAID')
                ->setPaymentIntentId($result['transaction_id'] ?? '')
                ->setPaymentMethod($paymentMethod)
                ->setBookingReference($result['booking_id']);

        if ($bookingDate) {
            $booking->setBookingDate(new \DateTime($bookingDate));
        }

        $em->persist($booking);
        $em->flush();

        if ($this->mailerService) {
            $this->mailerService->sendReservationConfirmation(
                $user->getEmail(),
                $user->getFullName(),
                $a->getTitle(),
                $result['booking_id'],
                $bookingDate ?: date('d/m/Y'),
                $totalPrice,
                'ACTIVITÉ'
            );
        }

        $this->addFlash('success', '✅ Activité reservée et payée avec succès!');
        return $this->redirectToRoute('payment_confirmation', ['bookingId' => $result['booking_id']]);
    }

    private function processPayment(array $bookingData, string $paymentMethod, User $user): array
    {
        $amount = $bookingData['total_amount'];
        
        if ($paymentMethod === 'paypal') {
            $orderResult = $this->paymentService->createPaypalOrder($amount, 'EUR', $bookingData['service_name']);
            if (!$orderResult['success']) {
                return ['success' => false, 'error' => $orderResult['error'] ?? 'Erreur PayPal'];
            }
            
            $captureResult = $this->paymentService->capturePaypalOrder($orderResult['order_id'] ?? '');
            if (!$captureResult['success']) {
                return ['success' => false, 'error' => 'Échec de la capture PayPal'];
            }
            
            $bookingData['payment_method'] = 'paypal';
            $bookingData['order_id'] = $orderResult['order_id'] ?? '';
        } else {
            $stripeResult = $this->paymentService->createStripePaymentIntent($amount, 'eur', $bookingData['service_name']);
            if (!$stripeResult['success']) {
                return ['success' => false, 'error' => $stripeResult['error'] ?? 'Erreur Stripe'];
            }
            
            $bookingData['payment_method'] = 'stripe';
            $bookingData['payment_intent_id'] = $stripeResult['payment_intent_id'] ?? '';
        }

        $receipt = $this->receiptService->createReceipt($bookingData);
        
        return [
            'success' => true,
            'booking_id' => $receipt['booking_id'],
            'transaction_id' => $bookingData['order_id'] ?? $bookingData['payment_intent_id'] ?? '',
            'receipt' => $receipt['receipt']
        ];
    }

    #[Route('/{id}/avis', name: 'activity_review', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addReview(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();

        /** @var User $user */
        $user = $this->getUser();
        $existingReview = $em->getRepository(Review::class)->findOneBy(['activity' => $a, 'user' => $user]);
        if ($existingReview) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour cette activité.');
            return $this->redirectToRoute('activity_show', ['id' => $id]);
        }

        $comment = (string)$request->request->get('comment', '');
        
        if ($this->moderationService) {
            $moderationResult = $this->moderationService->analyzeContent($comment);
            
            if ($moderationResult['has_offensive']) {
                $this->addFlash('error', 'Votre avis contient des mots inappropriés. Veuillez le reformuler.');
                return $this->redirectToRoute('activity_show', ['id' => $id]);
            }
            
            $comment = $this->moderationService->maskWords($comment);
        }

        $review = new Review();
        $review->setActivity($a)
               ->setUser($user)
               ->setAuthor($user->getFullName())
               ->setRating((int)$request->request->get('rating', 5))
               ->setComment($comment);

        if ($this->sentimentService && !empty($comment)) {
            $analysis = $this->sentimentService->analyze($comment);
            $review->setSentimentFromAnalysis($analysis);
        }

        $em->persist($review);
        $em->flush();

        $this->addFlash('success', '⭐ Votre avis activité a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/api/recommended', name: 'activity_recommended', methods: ['GET'])]
    public function getRecommended(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $em = $this->em;
        $activities = $em->getRepository(Activity::class)->findBy(['actif' => true]);
        
        $bookings = $em->getRepository(Booking::class)->findBy(['user' => $user]);
        $userCategories = [];
        foreach ($bookings as $booking) {
            $activity = $booking->getActivity();
            if ($activity && $activity->getCategory()) {
                $userCategories[] = $activity->getCategory();
            }
        }

        $scored = [];
        foreach ($activities as $activity) {
            $score = 0;
            if ($activity->getCategory() && in_array($activity->getCategory(), $userCategories)) {
                $score += 3;
            }
            $bookingCount = $activity->getBookings() ? $activity->getBookings()->count() : 0;
            if ($bookingCount > 3) $score += 1;
            if ($score > 0) {
                $scored[] = ['activity' => $activity, 'score' => $score];
            }
        }

        usort($scored, fn($a, $b) => $b['score'] - $a['score']);
        $results = array_slice($scored, 0, 6);

        return new JsonResponse([
            'activities' => array_map(fn($item) => [
                'id' => $item['activity']->getId(),
                'title' => $item['activity']->getTitle(),
                'price' => $item['activity']->getPrice(),
                'image' => $item['activity']->getImage(),
                'category' => $item['activity']->getCategory(),
                'lieu' => $item['activity']->getLieu(),
            ], $results)
        ]);
    }

    #[Route('/api/trending', name: 'activity_trending', methods: ['GET'])]
    public function getTrending(): JsonResponse
    {
        $em = $this->em;
        $activities = $em->getRepository(Activity::class)->findBy(['actif' => true]);

        // Sort by popularity
        usort($activities, function($a, $b) {
            return 0; // Keep original order for now
        });

        return new JsonResponse([
            'activities' => array_map(function($a) {
                return [
                    'id' => $a->getId(),
                    'title' => $a->getTitle(),
                    'price' => $a->getPrice(),
                    'image' => $a->getImage(),
                    'category' => $a->getCategory(),
                    'lieu' => $a->getLieu(),
                ];
            }, array_slice($activities, 0, 6))
        ]);
    }

    #[Route('/api/share/activity/{id}/forum', name: 'activity_share_forum', methods: ['POST'])]
    public function shareToForum(Activity $activity): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        // In production, create forum post here
        return new JsonResponse(['success' => true, 'post_id' => 1]);
    }

    #[Route('/api/share/activity/{id}/message', name: 'activity_share_message', methods: ['POST'])]
    public function shareToMessage(Activity $activity, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        $conversationId = $request->request->get('conversation_id');
        if (!$conversationId) {
            return new JsonResponse(['error' => 'Conversation ID required'], 400);
        }

        $conversation = $em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        // Create message with activity preview
        $message = new Message();
        $message->setSender($user);
        $message->setConversation($conversation);
        $message->setContent(json_encode([
            'type' => 'activity',
            'activity_id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'image' => $activity->getImage(),
            'link' => '/activites/' . $activity->getId()
        ]));
        $message->setCreatedAt(new \DateTime());

        $em->persist($message);
        $em->flush();

        return new JsonResponse(['success' => true, 'message_id' => $message->getId()]);
    }
}
