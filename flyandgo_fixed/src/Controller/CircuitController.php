<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Circuit;
use App\Entity\CircuitAvis;
use App\Entity\ReservationCircuit;
use App\Repository\ReservationCircuitRepository;
use App\Entity\User;
use App\Repository\CircuitRepository;
use App\Service\CircuitAiPlanner;
use App\Service\SentimentService;
use App\Service\Api\SherpaService;
use App\Service\Api\WeatherApiService;
use App\Service\Api\MapboxService;
use App\Service\Api\MailerService;
use App\Service\Api\PaymentService;
use App\Service\ReceiptService;
use App\Service\CircuitPdfService;
use App\Service\ContentModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/circuits')]
class CircuitController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ReceiptService $receiptService,
        private readonly WeatherApiService $weatherService,
        private readonly MapboxService $mapboxService,
        private readonly SherpaService $sherpaService,
        private readonly ?MailerService $mailerService = null,
        private readonly ?CircuitPdfService $circuitPdfService = null,
        private readonly ?SentimentService $sentimentService = null,
        private ?ContentModerationService $moderationService = null,
    ) {}

    #[Route('', name: 'circuit_index', methods: ['GET'])]
    public function index(Request $request, CircuitRepository $repo, PaginatorInterface $paginator, ReservationCircuitRepository $resRepo): Response
    {
        $q = $request->query->get('q');
        $difficulte = $request->query->get('difficulte');
        $destination = $request->query->get('destination');
        $source = $request->query->get('source');

        $qb = $repo->createQueryBuilder('c')->where('c.actif = 1')->orderBy('c.id', 'DESC');
        if ($q) {
            $qb->andWhere('c.titre LIKE :q OR c.destination LIKE :q')->setParameter('q', '%' . $q . '%');
        }
        if ($difficulte) {
            $qb->andWhere('c.difficulte = :diff')->setParameter('diff', $difficulte);
        }
        if ($destination) {
            $qb->andWhere('c.destination LIKE :dest')->setParameter('dest', '%' . $destination . '%');
        }
        if ($source) {
            $qb->andWhere('c.sourceType = :source')->setParameter('source', $source);
        }

        $circuits = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 8);

        $weatherData = [];
        $destinations = ['Djerba', 'Tunis', 'Sousse', 'Hammamet', 'Tozeur', 'Kairouan'];
        foreach ($destinations as $dest) {
            $weatherData[$dest] = $this->weatherService->getCurrentWeather($dest);
        }

        $userReservations = [];
        if ($this->getUser()) {
            $reservations = $resRepo->findBy(['user' => $this->getUser()]);
            foreach ($reservations as $res) {
                if ($res->getDateReservation()) {
                    $userReservations[] = [
                        'date' => $res->getDateReservation()->format('Y-m-d'),
                        'circuit' => $res->getCircuit()?->getTitre() ?: 'Circuit',
                        'status' => $res->getStatut() ?: 'Confirmé'
                    ];
                }
            }
        }

        $userCircuits = [];
        if ($this->getUser() instanceof User) {
            $userCircuitsData = $repo->findBy(['creator' => $this->getUser()], ['id' => 'DESC']);
            foreach ($userCircuitsData as $uc) {
                $avis = $uc->getAvis()->toArray();
                $userCircuits[] = [
                    'circuit' => $uc,
                    'avis' => array_map(fn($a) => [
                        'id' => $a->getId(),
                        'author' => $a->getAuthor(),
                        'rating' => $a->getRating(),
                        'comment' => $a->getComment(),
                        'createdAt' => $a->getCreatedAt(),
                        'sentimentLabel' => $a->getSentimentLabel(),
                    ], $avis),
                    'nbAvis' => count($avis),
                    'noteMoyenne' => $uc->getMoyenneNotes(),
                ];
            }
        }

        return $this->render('circuit/index.html.twig', [
            'circuits' => $circuits,
            'q' => $q,
            'difficulte' => $difficulte,
            'destination' => $destination,
            'source' => $source,
            'weatherData' => $weatherData,
            'userReservations' => $userReservations,
            'userCircuits' => $userCircuits,
        ]);
    }

    #[Route('/personnalise', name: 'circuit_ai_create', methods: ['GET', 'POST'])]
    public function createAi(Request $request, CircuitAiPlanner $planner, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isMethod('POST')) {
            /** @var User $user */
            $user = $this->getUser();
            $payload = [
                'destination' => $request->request->get('destination'),
                'depart' => $request->request->get('depart', 'Tunis'),
                'style' => $request->request->get('style', 'Découverte'),
                'budget' => $request->request->get('budget', 'Moyen'),
                'participants' => $request->request->get('participants', 2),
                'jours' => $request->request->get('jours', 3),
                'date_depart' => $request->request->get('date_depart'),
                'date_retour' => $request->request->get('date_retour'),
                'stops' => $request->request->get('stops', '[]'),
                'hebergement' => $request->request->get('hebergement', 'Hôtel'),
                'transport' => $request->request->get('transport', 'Voiture'),
            ];

            $result = $planner->generate($payload);

            $circuit = (new Circuit())
                ->setTitre($result['titre'] ?? '')
                ->setDescription($result['description'] ?? null)
                ->setDuree((int) ($result['duree'] ?? 1))
                ->setPrix((float) ($result['prix'] ?? 0))
                ->setDifficulte($result['difficulte'] ?? 'Moyen')
                ->setDepart($result['depart'] ?? 'Tunis')
                ->setDestination($result['destination'] ?? '')
                ->setPlacesDisponibles((int) ($result['places'] ?? 10))
                ->setActif(true)
                ->setIsCustom(true)
                ->setIsAiGenerated(true)
                ->setSourceType('custom')
                ->setGeneratedContext($result['generated_context'])
                ->setCreator($user)
                ->setLatitude($result['latitude'] ?? null)
                ->setLongitude($result['longitude'] ?? null)
                ->setStops($result['stops'] ?? [])
                ->setTotalDistance($result['distance'] ?? null);

            $em->persist($circuit);
            $em->flush();

            $this->addFlash('success', '🧠 Circuit sur mesure généré avec succès !');
            return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);
        }

        return $this->render('circuit/create_ai.html.twig');
    }

    #[Route('/{id}', name: 'circuit_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(int $id, CircuitRepository $repo): Response
    {
        $c = $repo->find($id);
        if (!$c) {
            throw $this->createNotFoundException();
        }

        if ($c->getSourceType() !== 'admin') {
            $user = $this->getUser();
            if (!$user instanceof User || $c->getCreator()?->getId() !== $user->getId()) {
                throw $this->createNotFoundException();
            }
        }

        $similar = $repo->findSimilar($c, 4);

        $context = null;
        if ($c->getGeneratedContext()) {
            $context = json_decode($c->getGeneratedContext(), true);
        }

        $weather = null;
        $forecast = [];
        if ($c->getDestination()) {
            $weather = $this->weatherService->getCurrentWeather($c->getDestination());
            $forecast = $this->weatherService->getForecast($c->getDestination(), 5);
        }

        $mapData = null;
        $staticMapUrl = null;
        $storytellingConfig = null;
        $segmentRequirements = [];

        if ($c->getDestination()) {
            $geo = $this->mapboxService->geocode($c->getDestination());
            if ($geo['success'] ?? false) {
                $mapData = [
                    'lat' => $geo['lat'],
                    'lng' => $geo['lng'],
                    'destination' => $c->getDestination(),
                    'depart' => $c->getDepart(),
                    'publicToken' => $this->mapboxService->getPublicToken(),
                ];

                // Generate static map with route if stops exist
                if (!empty($c->getStops())) {
                    $staticMapUrl = $this->mapboxService->getStaticMapWithRoute($c->getStops());
                    $storytellingConfig = $this->mapboxService->getStorytellingConfig($c);
                    
                    // Get segment requirements from Sherpa
                    $waypoints = array_merge([$c->getDepart()], $c->getStops(), [$c->getDestination()]);
                    $segmentRequirements = $this->sherpaService->getCircuitRequirements($waypoints);
                }
            }
        }

        return $this->render('circuit/show.html.twig', [
            'circuit' => $c,
            'similarCircuits' => $similar,
            'aiContext' => $context,
            'weather' => $weather,
            'forecast' => $forecast,
            'mapData' => $mapData,
            'staticMapUrl' => $staticMapUrl,
            'storytellingConfig' => $storytellingConfig,
            'segmentRequirements' => $segmentRequirements,
        ]);
    }

    #[Route('/{id}/pdf', name: 'circuit_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function downloadPdf(int $id, CircuitRepository $repo): Response
    {
        $circuit = $repo->find($id);
        if (!$circuit) {
            throw $this->createNotFoundException();
        }

        if (!$this->circuitPdfService) {
            throw $this->createNotFoundException('Service PDF non disponible');
        }

        return $this->circuitPdfService->generateAndDownload($circuit);
    }

    #[Route('/{id}/reserver', name: 'circuit_reserver', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function reserver(int $id, Request $request, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $c = $repo->find($id);
        if (!$c) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $dateDepart = $request->request->get('date_depart');
        $dateRetour = $request->request->get('date_retour');
        $nbPersonnes = (int) $request->request->get('nb_personnes', 1);
        $paymentMethod = $request->request->get('payment_method', 'stripe');
        
        $montant = $nbPersonnes * $c->getPrix();
        
        $bookingData = [
            'type' => 'CIRCUIT',
            'service_name' => $c->getTitre(),
            'service_date' => $dateDepart ?: date('Y-m-d'),
            'total_amount' => $montant,
            'unit_price' => $c->getPrix(),
            'quantity' => $nbPersonnes,
            'customer_name' => $user->getFullName(),
            'customer_email' => $user->getEmail(),
            'customer_phone' => $user->getTelephone() ?? '',
            'payment_method' => $paymentMethod,
            'circuit_id' => $c->getId(),
            'user_id' => $user->getId(),
            'date_depart' => $dateDepart,
            'date_retour' => $dateRetour,
        ];

        $result = $this->processPayment($bookingData, $paymentMethod, $user);

        if (!$result['success']) {
            $this->addFlash('error', 'Paiement échoué: ' . ($result['error'] ?? 'Erreur inconnue'));
            return $this->redirectToRoute('circuit_show', ['id' => $id]);
        }

        $res = new ReservationCircuit();
        $res->setCircuit($c)
            ->setUser($user)
            ->setNomClient($user->getFullName())
            ->setEmailClient($user->getEmail())
            ->setTelephone($user->getTelephone())
            ->setDateReservation(new \DateTimeImmutable($dateDepart ?: 'now'))
            ->setNbPersonnes($nbPersonnes)
            ->setMontantTotal($montant)
            ->setStatut('CONFIRME');

        $em->persist($res);
        $em->flush();

        if ($this->mailerService) {
            $this->mailerService->sendReservationConfirmation(
                $user->getEmail(),
                $user->getFullName(),
                $c->getTitre(),
                $result['booking_id'],
                $dateDepart ?: date('d/m/Y'),
                $montant,
                'CIRCUIT'
            );
        }

        $this->addFlash('success', '✅ Circuit reservé et payé avec succès!');
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

    #[Route('/{id}/avis', name: 'circuit_review', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addReview(int $id, Request $request, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $circuit = $repo->find($id);
        if (!$circuit) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $existingReview = $em->getRepository(CircuitAvis::class)->findOneBy(['circuit' => $circuit, 'user' => $user]);
        if ($existingReview) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour ce circuit.');
            return $this->redirectToRoute('circuit_show', ['id' => $id]);
        }

        $comment = (string) $request->request->get('comment', '');
        
        if ($this->moderationService) {
            $moderationResult = $this->moderationService->analyzeContent($comment);
            
            if ($moderationResult['has_offensive']) {
                $this->addFlash('error', 'Votre avis contient des mots inappropriés. Veuillez le reformuler.');
                return $this->redirectToRoute('circuit_show', ['id' => $id]);
            }
            
            $comment = $this->moderationService->maskWords($comment);
        }

        $review = new CircuitAvis();
        $review->setCircuit($circuit)
            ->setUser($user)
            ->setAuthor($user->getFullName())
            ->setRating((int) $request->request->get('rating', 5))
            ->setComment($comment);

        if ($this->sentimentService && !empty($comment)) {
            $analysis = $this->sentimentService->analyze($comment);
            $review->setSentimentFromAnalysis($analysis);
        }

        $em->persist($review);
        $em->flush();

        $this->addFlash('success', '⭐ Votre avis a bien été enregistré.');
        return $this->redirectToRoute('circuit_show', ['id' => $id]);
    }
}
