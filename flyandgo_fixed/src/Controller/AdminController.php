<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Avis;
use App\Entity\CalendarEvent;
use App\Entity\Circuit;
use App\Entity\CircuitAvis;
use App\Entity\ForumPost;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\ReservationCircuit;
use App\Entity\TransportAvis;
use App\Entity\TransportOffer;
use App\Entity\TransportBooking;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\AvisRepository;
use App\Repository\BookingRepository;
use App\Repository\CalendarEventRepository;
use App\Repository\CircuitAvisRepository;
use App\Repository\CircuitRepository;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\HebergementRepository;
use App\Repository\ProfilVoyageurRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\TransportAvisRepository;
use App\Repository\TransportOfferRepository;
use App\Repository\TransportBookingRepository;
use App\Repository\UserRepository;
use App\Service\WeatherService;
use App\Service\Api\RapidApiHotelService;
use App\Service\RapidHotelService;
use App\Service\ExchangeRateService;
use App\Service\GeoIPService;
use App\Service\AnalyticsService;
use App\Service\GeocodingService;
use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private ?WeatherService $weatherService = null,
        private ?ExchangeRateService $exchangeRateService = null,
        private ?GeoIPService $geoIPService = null,
        private ?AnalyticsService $analyticsService = null,
        private ?GeocodingService $geocodingService = null,
        private ?RapidApiHotelService $rapidApiHotelService = null,
        private ?RapidHotelService $rapidHotelService = null,
    ) {}

    /* ══════════════════════ DASHBOARD ══════════════════════ */
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(
        HebergementRepository    $hebRepo,
        ReservationRepository    $resRepo,
        CircuitRepository        $circRepo,
        ReservationCircuitRepository $rcRepo,
        ActivityRepository       $actRepo,
        BookingRepository        $bookRepo,
        ForumPostRepository      $forumRepo,
        ForumCommentRepository   $commentRepo,
        AvisRepository           $avisRepo,
        CircuitAvisRepository    $circuitAvisRepo,
        UserRepository           $userRepo,
        ProfilVoyageurRepository $profilRepo,
        TransportOfferRepository $transportRepo
    ): Response {
        $reservations = $resRepo->findBy([], ['id' => 'DESC'], 1000);
        $bookings     = $bookRepo->findBy([], ['id' => 'DESC'], 1000);
        $rcList       = $rcRepo->findBy([], ['id' => 'DESC'], 1000);
        $posts        = $forumRepo->findAll();

        $revenus = 0;
        foreach ($reservations as $r) {
            $revenus += $r->getMontantTotal() ?? 0;
        }
        foreach ($bookings as $b) {
            $revenus += $b->getTotalPrice() ?? 0;
        }
        foreach ($rcList as $r) {
            $revenus += $r->getMontantTotal() ?? 0;
        }

        // Get all counts
        $totalCircuits = $circRepo->count([]);
        $totalHebergements = $hebRepo->count([]);
        $totalActivities = $actRepo->count([]);
        $totalTransports = $transportRepo->count([]);

        $revenusParMois = $resRepo->getRevenusParMois();
        $occParVille    = $resRepo->getTauxOccupationParVille();
        $topHeb         = $resRepo->getTopHebergements();
        $profilStats    = $profilRepo->getStatistics();

        // Nouvelles stats : par mois, catégorie, saison
        $monthlyData = ['labels' => [], 'values' => [], 'revenus' => []];
        $monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = (new \DateTime())->modify("first day of -$i months");
            $monthEnd = (new \DateTime())->modify("last day of -$i months");
            
            $monthReservations = array_filter($reservations, function($r) use ($monthStart, $monthEnd) {
                $created = $r->getCreatedAt();
                return $created >= $monthStart && $created <= $monthEnd;
            });
            
            $count = count($monthReservations);
            $revenu = 0;
            foreach ($monthReservations as $r) {
                $revenu += $r->getMontantTotal() ?? 0;
            }
            
            $monthlyData['labels'][] = $monthNames[(int)$monthStart->format('n') - 1];
            $monthlyData['values'][] = $count;
            $monthlyData['revenus'][] = $revenu;
        }

        // Stats par catégorie circuits
        $statsParCategorie = [];
        $categorieLabels = [];
        $categorieValues = [];
        try {
            $statsParCategorie = $circRepo->createQueryBuilder('c')
                ->select('c.type as categorie, COUNT(c.id) as total, AVG(c.prix) as avgPrix')
                ->andWhere('c.type IS NOT NULL')
                ->groupBy('c.type')
                ->getQuery()->getResult() ?? [];
            foreach ($statsParCategorie as $cat) {
                $categorieLabels[] = $cat['categorie'] ?? 'Autre';
                $categorieValues[] = (int) $cat['total'];
            }
        } catch (\Exception $e) {
            // Ignore query errors
        }

        // Stats par saison
        $saisons = [
            'Hiver' => [12, 1, 2],
            'Printemps' => [3, 4, 5],
            'Été' => [6, 7, 8],
            'Automne' => [9, 10, 11],
        ];
        
        $saisonStats = [];
        foreach ($saisons as $saison => $months) {
            $saisonRes = array_filter($reservations, function($r) use ($months) {
                return in_array((int)$r->getCreatedAt()->format('n'), $months);
            });
            $count = count($saisonRes);
            $revenu = 0;
            foreach ($saisonRes as $r) {
                $revenu += $r->getMontantTotal() ?? 0;
            }
            $saisonStats[] = [
                'saison' => $saison,
                'label' => $saison,
                'count' => $count,
                'revenu' => $revenu
            ];
        }

        // Stats hébergements par type
        $hebTypes = [];
        $hebTypeLabels = [];
        $hebTypeValues = [];
        try {
            $hebTypes = $hebRepo->createQueryBuilder('h')
                ->select('h.type as type, COUNT(h.id) as total')
                ->andWhere('h.type IS NOT NULL')
                ->groupBy('h.type')
                ->getQuery()->getResult() ?? [];
            foreach ($hebTypes as $type) {
                $hebTypeLabels[] = $type['type'] ?? 'Autre';
                $hebTypeValues[] = (int) $type['total'];
            }
        } catch (\Exception $e) {}

        // Stats activités par catégorie
        $actCats = [];
        $actCatLabels = [];
        $actCatValues = [];
        try {
            $actCats = $actRepo->createQueryBuilder('a')
                ->select('a.category as categorie, COUNT(a.id) as total')
                ->andWhere('a.category IS NOT NULL')
                ->groupBy('a.category')
                ->getQuery()->getResult() ?? [];
            foreach ($actCats as $cat) {
                $actCatLabels[] = $cat['categorie'] ?? 'Autre';
                $actCatValues[] = (int) $cat['total'];
            }
        } catch (\Exception $e) {}

        $totalForumComments = $commentRepo->count([]);
        $todayReservations = count(array_filter($reservations, fn($r) => $r->getCreatedAt() >= new \DateTime('today')));
        $todayBookings = count(array_filter($bookings, fn($b) => $b->getCreatedAt() >= new \DateTime('today')));

        $destinations = ['Djerba', 'Tunis', 'Sousse', 'Carthage', 'Sfax'];
        $weatherData = [];
        foreach ($destinations as $dest) {
            if ($this->weatherService) {
                $weatherData[$dest] = $this->weatherService->getCurrentWeather($dest);
            }
        }

        $revenusMultiDevises = [];
        if ($this->exchangeRateService) {
            $revenusMultiDevises = $this->exchangeRateService->getRevenueInMultipleCurrencies($revenus);
        }

        $geoStats = [];
        if ($this->geoIPService) {
            $geoStats = [
                'countries' => $this->geoIPService->getCountryStats(),
                'cities' => $this->geoIPService->getTopCities(),
            ];
        }

        return $this->render('admin/dashboard.html.twig', [
            'totalHebergements'  => $totalHebergements,
            'totalReservations'  => count($reservations) + count($bookings) + count($rcList),
            'totalCircuits'      => $totalCircuits,
            'totalActivities'    => $totalActivities,
            'totalTransports'    => $totalTransports,
            'totalUsers'         => $userRepo->count([]),
            'totalForumPosts'    => count($posts),
            'totalForumComments' => $totalForumComments,
            'totalProfils'       => $profilStats['total'] ?? 0,
            'revenus'            => $revenus,
            'revenusMultiDevises' => $revenusMultiDevises,
            'moyenneAvis'        => $avisRepo->getMoyenneGenerale(),
            'moyenneBudget'      => $profilStats['averageBudget'] ?? 0,
            'pendingRes'         => count(array_filter($reservations, fn($r) => $r->getStatut() === 'EN_ATTENTE')),
            'pendingBook'        => count(array_filter($bookings, fn($b) => $b->getStatus() === 'PENDING')),
            'pendingPosts'       => count(array_filter($posts, fn($p) => $p->getStatus() === 'PENDING')),
            'recentReservations' => $resRepo->findBy([], ['id' => 'DESC'], 6),
            'recentBookings'     => $bookRepo->findBy([], ['id' => 'DESC'], 4),
            'recentPosts'        => $forumRepo->findBy([], ['createdAt' => 'DESC'], 4),
            'revenusParMois'     => $revenusParMois,
            'occParVille'        => $occParVille,
            'topHeb'             => $topHeb,
            'profilCountByType'  => $profilStats['countByType'] ?? [],
            'todayReservations'  => $todayReservations + $todayBookings,
            'weatherData'         => $weatherData,
            'geoStats'            => $geoStats,
            'conversionRate'      => $this->analyticsService ? $this->analyticsService->getConversionRate() : ['rate' => 2.5, 'trend' => 'up', 'totalViews' => 12500, 'totalBookings' => 312],
            'topSellingCircuits'  => $this->analyticsService ? $this->analyticsService->getTopSellingCircuits(5) : [],
            'trendingDestinations' => $this->analyticsService ? $this->analyticsService->getTrendingDestinations(6) : [],
            'revenueForecast'     => $this->analyticsService ? $this->analyticsService->getRevenueForecasting(6)['forecast'] ?? [] : [],
            'monthlyRevenue'      => $this->analyticsService ? $this->analyticsService->getRevenueForecasting(6)['avgMonthly'] ?? 0 : 0,
            'totalViews'          => $this->analyticsService ? $this->analyticsService->getConversionRate()['totalViews'] ?? 12500 : 12500,
            'growthRate'          => $this->analyticsService ? $this->analyticsService->getRevenueForecasting(6)['growthRate'] ?? 12 : 12,
            'forecastGrowth'      => $this->analyticsService ? $this->analyticsService->getRevenueForecasting(6)['growthRate'] ?? 8 : 8,
            'monthlyData'         => $monthlyData,
            'statsParCategorie'   => $statsParCategorie,
            'statsParCategorieLabels' => $categorieLabels,
            'statsParCategorieData' => $categorieValues,
            'categorieLabels'     => $categorieLabels,
            'categorieValues'     => $categorieValues,
            'saisonStats'        => $saisonStats,
            'hebTypeLabels'      => $hebTypeLabels,
            'hebTypeValues'      => $hebTypeValues,
            'actCatLabels'       => $actCatLabels,
            'actCatValues'       => $actCatValues,
            
            // Circuit statistics
            'circuitStatsByDifficulty' => $this->getCircuitStatsByDifficulty($circRepo),
            'circuitStatsByDuration' => $this->getCircuitStatsByDuration($circRepo),
            'adminCircuitsCount' => $circRepo->count(['sourceType' => 'admin']),
            'aiCircuitsCount' => $circRepo->count(['isAiGenerated' => true]),
            'customCircuitsCount' => $circRepo->count(['isCustom' => true]),
            'cheapCircuitsCount' => count($circRepo->createQueryBuilder('c')->andWhere('c.prix < 500')->getQuery()->getResult()),
            'midCircuitsCount' => count($circRepo->createQueryBuilder('c')->andWhere('c.prix >= 500 AND c.prix <= 1500')->getQuery()->getResult()),
            'premiumCircuitsCount' => count($circRepo->createQueryBuilder('c')->andWhere('c.prix > 1500')->getQuery()->getResult()),
            'positiveReviews' => $avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel IN (:pos)')->setParameter('pos', ['excellent','good','positive'])->getQuery()->getResult() ? count($avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel IN (:pos)')->setParameter('pos', ['excellent','good','positive'])->getQuery()->getResult()) : 0,
            'neutralReviews' => $avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel = :neu')->setParameter('neu', 'neutral')->getQuery()->getResult() ? count($avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel = :neu')->setParameter('neu', 'neutral')->getQuery()->getResult()) : 0,
            'negativeReviews' => $avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel IN (:neg)')->setParameter('neg', ['negative','bad'])->getQuery()->getResult() ? count($avisRepo->createQueryBuilder('a')->andWhere('a.sentimentLabel IN (:neg)')->setParameter('neg', ['negative','bad'])->getQuery()->getResult()) : 0,
            'circuitsWithDrop' => 0,
            'highCancellationRate' => 0,
            'healthyCircuits' => $circRepo->count([]),
            'circuitStatsByDestination' => $this->getCircuitStatsByDestination($circRepo),
            'topRatedCircuits' => $this->getTopRatedCircuits($circRepo),
            'circuitPriceAnalysis' => $this->getCircuitPriceAnalysis($circRepo),
            'circuitStatsByCapacity' => $this->getCircuitStatsByCapacity($circRepo),
            'circuitStatsBySeason' => $this->getCircuitStatsBySeason($rcRepo),
            'calendarEvents' => $this->getCalendarEvents($rcRepo),
            'chatbotAnalytics' => $this->getChatbotAnalytics(),
        ]);
    }
    
    private function getChatbotAnalytics(): array {
        return [
            'topIntents' => [
                ['intent' => 'Reservation circuit', 'count' => 145, 'percentage' => 35],
                ['intent' => 'Prix et tarifs', 'count' => 98, 'percentage' => 24],
                ['intent' => 'Disponibilite', 'count' => 76, 'percentage' => 18],
                ['intent' => 'Informations destination', 'count' => 52, 'percentage' => 13],
                ['intent' => 'Support client', 'count' => 41, 'percentage' => 10],
            ],
            'topKeywords' => [
                ['keyword' => 'circuit', 'count' => 234],
                ['keyword' => 'prix', 'count' => 187],
                ['keyword' => 'reservation', 'count' => 156],
                ['keyword' => 'djerba', 'count' => 134],
                ['keyword' => 'guide', 'count' => 98],
            ],
            'feedback' => [
                'positive' => 67,
                'negative' => 23,
                'neutral' => 10,
                'total' => 1000
            ],
            'avgRating' => 4.2,
            'totalConversations' => 1245,
            'issues' => [
                'unclear' => 45,
                'reformulation' => 78,
                'abandon' => 32,
            ],
            'volumeByHour' => [
                ['hour' => '00:00', 'count' => 12],
                ['hour' => '06:00', 'count' => 28],
                ['hour' => '09:00', 'count' => 89],
                ['hour' => '12:00', 'count' => 156],
                ['hour' => '15:00', 'count' => 178],
                ['hour' => '18:00', 'count' => 145],
                ['hour' => '21:00', 'count' => 67],
            ],
            'volumeByDay' => [
                ['day' => 'Lun', 'count' => 167],
                ['day' => 'Mar', 'count' => 189],
                ['day' => 'Mer', 'count' => 178],
                ['day' => 'Jeu', 'count' => 195],
                ['day' => 'Ven', 'count' => 234],
                ['day' => 'Sam', 'count' => 156],
                ['day' => 'Dim', 'count' => 126],
            ],
        ];
    }
    
    private function getCalendarEvents(ReservationCircuitRepository $rcRepo): array {
        $reservations = $rcRepo->findAll();
        $events = [];
        foreach ($reservations as $r) {
            $circuit = $r->getCircuit();
            $title = $circuit ? $circuit->getTitre() : 'Réservation #' . $r->getId();
            $dateD = $r->getDateDepart();
            if ($dateD) {
                $duree = $circuit ? ($circuit->getDuree() ?? 1) : 1;
                $dateF = (clone $dateD)->modify('+' . $duree . ' days');
                $events[] = [
                    'title' => $title,
                    'start' => $dateD->format('Y-m-d'),
                    'end' => $dateF->format('Y-m-d'),
                    'color' => in_array($r->getStatut(), ['CONFIRME', 'CONFIRMED']) ? '#10B981' : (in_array($r->getStatut(), ['ANNULE', 'CANCELLED']) ? '#EF4444' : '#1B3A6B')
                ];
            }
        }
        return $events;
    }
    
    private function getCircuitStatsByDifficulty($circRepo): array {
        $difficulties = ['Facile', 'Modéré', 'Difficile'];
        $stats = [];
        $total = $circRepo->count([]);
        foreach ($difficulties as $diff) {
            $count = $circRepo->count(['difficulte' => $diff]);
            $stats[] = [
                'difficulte' => $diff,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0
            ];
        }
        return $stats;
    }
    
    private function getCircuitStatsByDuration($circRepo): array {
        $durees = [
            ['duration' => '1-2 jours', 'min' => 1, 'max' => 2],
            ['duration' => '3-5 jours', 'min' => 3, 'max' => 5],
            ['duration' => '6-8 jours', 'min' => 6, 'max' => 8],
            ['duration' => '9+ jours', 'min' => 9, 'max' => 100]
        ];
        $stats = [];
        foreach ($durees as $d) {
            $count = $circRepo->createQueryBuilder('c')
                ->andWhere('c.duree >= :min AND c.duree <= :max')
                ->setParameter('min', $d['min'])
                ->setParameter('max', $d['max'])
                ->getQuery()->getResult();
            $avgPrice = $circRepo->createQueryBuilder('c')
                ->select('AVG(c.prix)')
                ->andWhere('c.duree >= :min AND c.duree <= :max')
                ->setParameter('min', $d['min'])
                ->setParameter('max', $d['max'])
                ->getQuery()->getSingleScalarResult() ?? 0;
            $stats[] = [
                'duration' => $d['duration'],
                'count' => count($count),
                'avgPrice' => round($avgPrice)
            ];
        }
        return $stats;
    }

    private function getCircuitStatsByDestination(CircuitRepository $circRepo): array {
        $circuits = $circRepo->findAll();
        $destCounts = [];
        foreach ($circuits as $c) {
            $dest = $c->getDestination() ?? 'Autre';
            if (!isset($destCounts[$dest])) {
                $destCounts[$dest] = ['destination' => $dest, 'count' => 0, 'minPrice' => PHP_INT_MAX, 'maxPrice' => 0, 'totalPrice' => 0];
            }
            $destCounts[$dest]['count']++;
            $price = $c->getPrix() ?? 0;
            $destCounts[$dest]['totalPrice'] += $price;
            if ($price > 0) {
                if ($price < $destCounts[$dest]['minPrice']) $destCounts[$dest]['minPrice'] = $price;
                if ($price > $destCounts[$dest]['maxPrice']) $destCounts[$dest]['maxPrice'] = $price;
            }
        }
        foreach ($destCounts as &$dc) {
            $dc['avgPrice'] = $dc['count'] > 0 ? round($dc['totalPrice'] / $dc['count']) : 0;
            if ($dc['minPrice'] === PHP_INT_MAX) $dc['minPrice'] = 0;
            unset($dc['totalPrice']);
        }
        usort($destCounts, fn($a, $b) => $b['count'] - $a['count']);
        return array_slice($destCounts, 0, 10);
    }

private function getTopRatedCircuits(CircuitRepository $circRepo): array {
        $circuits = $circRepo->findAll();
        if (empty($circuits)) {
            return [];
        }
        usort($circuits, fn($a, $b) => ($b->getNoteMoyenne() ?? 0) <=> ($a->getNoteMoyenne() ?? 0));
        $rated = [];
        foreach (array_slice($circuits, 0, 20) as $c) {
            $note = $c->getNoteMoyenne() ?? 0;
            $nbAvis = $c->getNbAvis() ?? 0;
            $rated[] = [
                'id' => $c->getId(),
                'name' => $c->getTitre() ?? 'Circuit #' . $c->getId(),
                'avgNote' => $note > 0 ? round($note, 1) : '-',
                'count' => $nbAvis,
                'destination' => $c->getDestination() ?? '-',
                'prix' => $c->getPrix() ?? 0
            ];
        }
        return array_slice($rated, 0, 10);
    }

    private function getCircuitPriceAnalysis(CircuitRepository $circRepo): array {
        $circuits = $circRepo->findAll();
        $prices = [];
        foreach ($circuits as $c) {
            $p = $c->getPrix();
            if ($p > 0) $prices[] = $p;
        }
        if (empty($prices)) {
            return ['min' => 0, 'max' => 0, 'avg' => 0, 'median' => 0, 'total' => 0];
        }
        sort($prices);
        $count = count($prices);
        return [
            'min' => min($prices),
            'max' => max($prices),
            'avg' => round(array_sum($prices) / $count),
            'median' => $prices[($count - 1) / 2],
            'total' => $count
        ];
    }

    private function getCircuitStatsByCapacity(CircuitRepository $circRepo): array {
        $ranges = [
            ['label' => '1-5 pers.', 'min' => 1, 'max' => 5],
            ['label' => '6-10 pers.', 'min' => 6, 'max' => 10],
            ['label' => '11-20 pers.', 'min' => 11, 'max' => 20],
            ['label' => '21+ pers.', 'min' => 21, 'max' => 100]
        ];
        $stats = [];
        foreach ($ranges as $r) {
            $count = $circRepo->createQueryBuilder('c')
                ->andWhere('c.placesDisponibles >= :min AND c.placesDisponibles <= :max')
                ->setParameter('min', $r['min'])
                ->setParameter('max', $r['max'])
                ->getQuery()->getResult();
            $stats[] = [
                'label' => $r['label'],
                'count' => count($count)
            ];
        }
        return $stats;
    }

    private function getCircuitStatsBySeason(ReservationCircuitRepository $rcRepo): array {
        $reservations = $rcRepo->findAll();
        $seasonCounts = ['Hiver' => 0, 'Printemps' => 0, 'Été' => 0, 'Automne' => 0];
        foreach ($reservations as $r) {
            $date = $r->getDateDepart() ?? $r->getCreatedAt();
            if ($date) {
                $month = (int) $date->format('n');
                if (in_array($month, [12, 1, 2])) $seasonCounts['Hiver']++;
                elseif (in_array($month, [3, 4, 5])) $seasonCounts['Printemps']++;
                elseif (in_array($month, [6, 7, 8])) $seasonCounts['Été']++;
                else $seasonCounts['Automne']++;
            }
        }
        $stats = [];
        foreach ($seasonCounts as $season => $count) {
            $stats[] = ['season' => $season, 'count' => $count];
        }
        return $stats;
    }

    /* ══════════════════════ USERS ══════════════════════ */
    #[Route('/users', name: 'admin_users')]
    public function users(Request $request, UserRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $active = $request->query->get('active', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('u');

        if ($q) {
            $qb->andWhere('u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($active !== '') {
            $qb->andWhere('u.actif = :active')->setParameter('active', $active === '1');
        }

        match ($sort) {
            'oldest' => $qb->orderBy('u.createdAt', 'ASC'),
            default => $qb->orderBy('u.createdAt', 'DESC'),
        };

        $users = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, );

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/user/{id}/toggle', name: 'admin_user_toggle', methods: ['POST'])]
    public function toggleUser(int $id, UserRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $user = $repo->find($id);
        if (!$user) return new JsonResponse(['error' => 'Not found'], 404);
        $user->setIsActive(!$user->isActive());
        $em->flush();
        return new JsonResponse(['active' => $user->isActive()]);
    }

    #[Route('/user/{id}/role', name: 'admin_user_role', methods: ['POST'])]
    public function toggleRole(int $id, Request $request, UserRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $user = $repo->find($id);
        if (!$user) return new JsonResponse(['error' => 'Not found'], 404);
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }
        $em->flush();
        return new JsonResponse(['roles' => $user->getRoles()]);
    }

    #[Route('/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(int $id, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $user = $repo->find($id);
        if ($user) { $em->remove($user); $em->flush(); }
        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(int $id, Request $request, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $user = $repo->find($id);
        if (!$user) throw $this->createNotFoundException('Utilisateur non trouvé');

        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom', ''));
            $prenom = trim($request->request->get('prenom', ''));
            $email = trim($request->request->get('email', ''));
            $telephone = trim($request->request->get('telephone', ''));
            $role = $request->request->get('role', 'ROLE_USER');

            if ($nom) $user->setNom($nom);
            if ($prenom) $user->setPrenom($prenom);
            if ($email) $user->setEmail($email);
            if ($telephone) $user->setTelephone($telephone);
            $user->setRoles([$role]);

            $em->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès !');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_edit.html.twig', ['user' => $user]);
    }

    /* ══════════════════════ HÉBERGEMENTS ══════════════════════ */
    #[Route('/hebergements', name: 'admin_hebergements')]
    public function hebergements(Request $request, HebergementRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $ville = $request->query->get('ville', '');
        $type = $request->query->get('type', '');
        $disponible = $request->query->get('disponible', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('h');

        if ($q) {
            $qb->andWhere('h.nom LIKE :q OR h.ville LIKE :q OR h.description LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($ville) {
            $qb->andWhere('h.ville = :ville')->setParameter('ville', $ville);
        }

        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }

        if ($disponible !== '') {
            $qb->andWhere('h.disponible = :disponible')->setParameter('disponible', $disponible === '1');
        }

        match ($sort) {
            'oldest' => $qb->orderBy('h.id', 'ASC'),
            'price_asc' => $qb->orderBy('h.prixParNuit', 'ASC'),
            'price_desc' => $qb->orderBy('h.prixParNuit', 'DESC'),
            default => $qb->orderBy('h.id', 'DESC'),
        };

        $hebergements = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, );

        return $this->render('admin/hebergements.html.twig', ['hebergements' => $hebergements]);
    }

    #[Route('/hebergement/new', name: 'admin_hebergement_new', methods: ['GET', 'POST'])]
    public function hebergementNew(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $h = new Hebergement();
            $this->fillHebergement($h, $request);
            
            $errors = $validator->validate($h);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                $this->addFlash('error', implode('<br>', $errorMessages));
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            
            $existingImage = $h->getImage();
            $file = $request->files->get('image');
            if (!$existingImage && (!$file || $file->getError() !== UPLOAD_ERR_OK)) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            
            $em->persist($h);
            $em->flush();
            $this->addFlash('success', '✅ Hébergement « ' . $h->getNom() . ' » créé !');
            return $this->redirectToRoute('admin_hebergements');
        }
        return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
    }

    #[Route('/hebergement/{id}/edit', name: 'admin_hebergement_edit', methods: ['GET', 'POST'])]
    public function hebergementEdit(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $h = $repo->find($id);
        if (!$h) throw $this->createNotFoundException();
        if ($request->isMethod('POST')) {
            $this->fillHebergement($h, $request);
            
            $errors = $validator->validate($h);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                $this->addFlash('error', implode('<br>', $errorMessages));
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            
            if (!$h->getImage()) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            
            $em->flush();
            $this->addFlash('success', '✅ Hébergement mis à jour !');
            return $this->redirectToRoute('admin_hebergements');
        }
        return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
    }

    #[Route('/hebergement/{id}/delete', name: 'admin_hebergement_delete', methods: ['POST'])]
    public function hebergementDelete(int $id, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $h = $repo->find($id);
        if ($h) { $em->remove($h); $em->flush(); }
        $this->addFlash('success', 'Hébergement supprimé.');
        return $this->redirectToRoute('admin_hebergements');
    }

    private function fillHebergement(Hebergement $h, Request $request): void
    {
        $h->setNom(trim($request->request->get('nom', '')))
          ->setVille(trim($request->request->get('ville', '')))
          ->setType($request->request->get('type', ''))
          ->setPrixParNuit((float)$request->request->get('prix_par_nuit', 0))
          ->setDescription($request->request->get('description') ?: null)
          ->setAdresse($request->request->get('adresse') ?: null)
          ->setCapacite($request->request->get('capacite') ? (int)$request->request->get('capacite') : null)
          ->setDisponible((bool)$request->request->get('disponible', true))
          ->setLatitude($request->request->get('latitude') ? (float)$request->request->get('latitude') : null)
          ->setLongitude($request->request->get('longitude') ? (float)$request->request->get('longitude') : null)
          ->setChambresDisponibles($request->request->get('chambres_disponibles') ? (int)$request->request->get('chambres_disponibles') : 1);

        // Handle main image
        $existingImage = $request->request->get('existing_image', '');
        $file = $request->files->get('image');
        
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) && $file->getSize() <= 5 * 1024 * 1024) {
                $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/hebergements';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $filename = uniqid('heb_') . '.' . $ext;
                $file->move($dir, $filename);
                $h->setImage('uploads/hebergements/' . $filename);
            }
        } elseif ($existingImage) {
            $h->setImage($existingImage);
        } else {
            $oldImage = $h->getImage();
            if ($oldImage) {
                $h->setImage($oldImage);
            }
        }
        
        // Handle gallery photos
        $gallery = $h->getGaleriePhotos() ?: [];
        $galleryFiles = $request->files->get('galerie_photos');
        
        if ($galleryFiles && is_array($galleryFiles)) {
            $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/hebergements/galerie';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            foreach ($galleryFiles as $galFile) {
                if ($galFile && $galFile->getError() === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($galFile->getClientOriginalName(), PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $filename = uniqid('gal_') . '.' . $ext;
                        $galFile->move($dir, $filename);
                        $gallery[] = 'uploads/hebergements/galerie/' . $filename;
                    }
                }
            }
        }
        
        // Handle gallery photo deletion
        $galleryToDelete = $request->request->get('galerie_to_delete');
        if ($galleryToDelete) {
            $toDelete = array_filter(array_map('trim', explode(',', $galleryToDelete)));
            $projectDir = $this->getParameter('kernel.project_dir');
            
            foreach ($toDelete as $photoToDelete) {
                $photoPath = $projectDir . '/public/' . $photoToDelete;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $gallery = array_filter($gallery, fn($p) => $p !== $photoToDelete);
            }
            $gallery = array_values($gallery);
        }
        
        if (!empty($gallery)) {
            $h->setGaleriePhotos($gallery);
        }
    }

    #[Route('/hebergement/import-rapidapi', name: 'admin_hebergement_import_rapidapi', methods: ['GET', 'POST'])]
    public function importFromRapidApi(Request $request, EntityManagerInterface $em): Response
    {
        $ville = trim($request->request->get('ville', $request->query->get('ville', '')));
        
        if (empty($ville)) {
            $this->addFlash('error', 'Veuillez entrer une ville.');
            return $this->redirectToRoute('admin_hebergements');
        }
        
        $result = $this->rapidHotelService->getHotels($ville, 2);
        
        if (!$result['success'] || empty($result['hotels'])) {
            $this->addFlash('error', 'Aucun hôtel trouvé pour: ' . $ville . ' (' . ($result['error'] ?? 'Erreur API') . ')');
            return $this->redirectToRoute('admin_hebergements');
        }
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($result['hotels'] as $hotel) {
            $exists = $em->getRepository(Hebergement::class)->findOneBy([
                'nom' => $hotel['nom'],
                'ville' => $hotel['ville']
            ]);
            
            if ($exists) {
                $skipped++;
                continue;
            }
            
            $h = new Hebergement();
            $h->setNom($hotel['nom'])
              ->setVille($hotel['ville'])
              ->setType('Hôtel')
              ->setAdresse($hotel['adresse'] ?? '')
              ->setPrixParNuit((int)($hotel['prix'] ?? rand(50, 300)))
              ->setCapacite(rand(5, 30))
              ->setChambresDisponibles(rand(1, 10))
              ->setNote($hotel['note'] ?? 0)
              ->setEquipements($hotel['equipements'] ?? [])
              ->setDisponible(true);
            
            if (!empty($hotel['imageUrl'])) {
                $h->setImage($hotel['imageUrl']);
            }
            
            $em->persist($h);
            $imported++;
        }
        
        $em->flush();
        $this->addFlash('success', "Importation réussie: $imported hôtels importés de $ville, $skipped ignorés.");
        
        return $this->redirectToRoute('admin_hebergements');
    }

    #[Route('/hebergements/search-rapidapi', name: 'admin_hebergements_search_rapidapi')]
    public function searchRapidApi(Request $request): JsonResponse
    {
        $ville = trim($request->query->get('ville', ''));
        
        if (empty($ville)) {
            return new JsonResponse(['success' => false, 'error' => 'Ville requise']);
        }
        
        $result = $this->rapidHotelService->getHotels($ville, 2);
        
        return new JsonResponse($result);
    }
    
    #[Route('/hebergements/test-api', name: 'admin_hebergements_test_api')]
    public function testApi(Request $request): JsonResponse
    {
        $ville = trim($request->query->get('ville', 'Paris'));
        $checkin = date('Y-m-d', strtotime('+1 day'));
        $checkout = date('Y-m-d', strtotime('+3 days'));
        
        $apiKey = $_ENV['RAPIDAPI_KEY'] ?? '';
        $host = $_ENV['RAPIDAPI_HOST'] ?? 'hotels-com-provider.p.rapidapi.com';
        
        $testUrl = 'https://' . $host . '/v3/hotels/search?query=' . urlencode($ville) . '&checkin=' . $checkin . '&checkout=' . $checkout . '&adults=2&currency=EUR';
        
        return new JsonResponse([
            'api_key_set' => !empty($apiKey),
            'test_url' => $testUrl,
            'debug' => [
                'ville' => $ville,
                'checkin' => $checkin,
                'checkout' => $checkout
            ]
        ]);
    }

    /* ══════════════════════ RÉSERVATIONS HÉBERGEMENT ══════════════════════ */
    #[Route('/reservations', name: 'admin_reservations')]
    public function reservations(Request $request, ReservationRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $statut = $request->query->get('statut', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.hebergement', 'h');

        if ($q) {
            $qb->andWhere('r.nomClient LIKE :q OR r.emailClient LIKE :q OR h.nom LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($statut) {
            $qb->andWhere('r.statut = :statut')->setParameter('statut', $statut);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('r.id', 'ASC'),
            default => $qb->orderBy('r.id', 'DESC'),
        };

        $reservations = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, );

        return $this->render('admin/reservations.html.twig', ['reservations' => $reservations]);
    }

    #[Route('/reservation/{id}/statut', name: 'admin_reservation_statut', methods: ['POST'])]
    public function reservationStatut(int $id, Request $request, ReservationRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $r = $repo->find($id);
        if (!$r) return new JsonResponse(['error' => 'Not found'], 404);
        $r->setStatut($request->request->get('statut', 'EN_ATTENTE'));
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/reservation/{id}/delete', name: 'admin_reservation_delete', methods: ['POST'])]
    public function reservationDelete(int $id, ReservationRepository $repo, EntityManagerInterface $em): Response
    {
        $r = $repo->find($id);
        if ($r) { $em->remove($r); $em->flush(); }
        $this->addFlash('success', 'Réservation supprimée.');
        return $this->redirectToRoute('admin_reservations');
    }

    /* ══════════════════════ AVIS ══════════════════════ */
    #[Route('/avis', name: 'admin_avis')]
    public function avis(Request $request, AvisRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');

        $qb = $repo->createQueryBuilder('a')
            ->leftJoin('a.hebergement', 'h');

        if ($q) {
            $qb->andWhere('a.auteur LIKE :q OR a.commentaire LIKE :q OR h.nom LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('a.createdAt', 'DESC');

        $avis = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 12, );

        return $this->render('admin/avis.html.twig', ['avis' => $avis]);
    }

    #[Route('/avis/{id}/delete', name: 'admin_avis_delete', methods: ['POST'])]
    public function avisDelete(int $id, AvisRepository $repo, EntityManagerInterface $em): Response
    {
        $a = $repo->find($id);
        if ($a) { $em->remove($a); $em->flush(); }
        $this->addFlash('success', 'Avis supprimé.');
        return $this->redirectToRoute('admin_avis');
    }

    /* ══════════════════════ CIRCUITS ══════════════════════ */
    #[Route('/circuits', name: 'admin_circuits')]
    public function circuits(Request $request, CircuitRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $difficulte = $request->query->get('difficulte', '');
        $source = $request->query->get('source', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('c');

        if ($q) {
            $qb->andWhere('c.titre LIKE :q OR c.destination LIKE :q OR c.description LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($difficulte) {
            $qb->andWhere('c.difficulte = :difficulte')->setParameter('difficulte', $difficulte);
        }

        if ($source) {
            $qb->andWhere('c.sourceType = :source')->setParameter('source', $source);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('c.id', 'ASC'),
            'price_asc' => $qb->orderBy('c.prix', 'ASC'),
            'price_desc' => $qb->orderBy('c.prix', 'DESC'),
            default => $qb->orderBy('c.id', 'DESC'),
        };

        $circuits = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, );

        return $this->render('admin/circuits.html.twig', ['circuits' => $circuits]);
    }

    #[Route('/circuit/new', name: 'admin_circuit_new', methods: ['GET', 'POST'])]
    public function circuitNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $titre = trim($request->request->get('titre', ''));
            $prix = $request->request->get('prix', '');
            
            if (!$titre) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => null]);
            }
            if ($prix === '' || (float)$prix <= 0) {
                $this->addFlash('error', 'Le prix doit être supérieur à 0.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => null]);
            }
            
            $file = $request->files->get('image');
            if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => null]);
            }
            
            $c = new Circuit();
            $this->fillCircuit($c, $request);
            $em->persist($c);
            $em->flush();
            $this->addFlash('success', '✅ Circuit « ' . $c->getTitre() . ' » créé !');
            return $this->redirectToRoute('admin_circuits');
        }
        return $this->render('admin/circuit_form.html.twig', ['circuit' => null]);
    }

    #[Route('/circuit/{id}/edit', name: 'admin_circuit_edit', methods: ['GET', 'POST'])]
    public function circuitEdit(int $id, Request $request, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $c = $repo->find($id);
        if (!$c) throw $this->createNotFoundException();
        if ($request->isMethod('POST')) {
            $titre = trim($request->request->get('titre', ''));
            $prix = $request->request->get('prix', '');
            
            if (!$titre) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => $c]);
            }
            if ($prix === '' || (float)$prix <= 0) {
                $this->addFlash('error', 'Le prix doit être supérieur à 0.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => $c]);
            }
            
            $file = $request->files->get('image');
            if (!$c->getImage() && (!$file || $file->getError() !== UPLOAD_ERR_OK)) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/circuit_form.html.twig', ['circuit' => $c]);
            }
            
            $this->fillCircuit($c, $request);
            $em->flush();
            $this->addFlash('success', '✅ Circuit mis à jour !');
            return $this->redirectToRoute('admin_circuits');
        }
        return $this->render('admin/circuit_form.html.twig', ['circuit' => $c]);
    }

    #[Route('/circuit/{id}/delete', name: 'admin_circuit_delete', methods: ['POST'])]
    public function circuitDelete(int $id, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $c = $repo->find($id);
        if ($c) { $em->remove($c); $em->flush(); }
        $this->addFlash('success', 'Circuit supprimé.');
        return $this->redirectToRoute('admin_circuits');
    }

    private function fillCircuit(Circuit $c, Request $request): void
    {
        $c->setTitre(trim($request->request->get('titre', '')))
          ->setDescription($request->request->get('description') ?: null)
          ->setPlanB($request->request->get('planB') ?: null)
          ->setDuree($request->request->get('duree') ?: null)
          ->setPrix((float)$request->request->get('prix', 0))
          ->setDifficulte($request->request->get('difficulte') ?: null)
          ->setPlacesDisponibles($request->request->get('places') ? (int)$request->request->get('places') : null)
          ->setDepart($request->request->get('depart') ?: null)
          ->setDestination($request->request->get('destination') ?: null)
          ->setActif((bool)$request->request->get('actif', true));

        $file = $request->files->get('image');
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) && $file->getSize() <= 5 * 1024 * 1024) {
                $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/circuits';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $filename = uniqid('circ_') . '.' . $ext;
                $file->move($dir, $filename);
                $c->setImage('uploads/circuits/' . $filename);
            }
        }
    }

    /* ══════════════════════ TRANSPORTS ══════════════════════ */
    #[Route('/transports', name: 'admin_transports')]
    public function transports(Request $request, TransportOfferRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $type = $request->query->get('type', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('t');

        if ($q) {
            $qb->andWhere('t.companyName LIKE :q OR t.departureCity LIKE :q OR t.arrivalCity LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($type) {
            $qb->andWhere('t.transportType = :type')->setParameter('type', $type);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('t.id', 'ASC'),
            'price_asc' => $qb->orderBy('t.price', 'ASC'),
            'price_desc' => $qb->orderBy('t.price', 'DESC'),
            'date_asc' => $qb->orderBy('t.departureDatetime', 'ASC'),
            'date_desc' => $qb->orderBy('t.departureDatetime', 'DESC'),
            default => $qb->orderBy('t.id', 'DESC'),
        };

        $transports = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, );

        return $this->render('admin/transports.html.twig', ['transports' => $transports]);
    }

    #[Route('/transport/new', name: 'admin_transport_new', methods: ['GET', 'POST'])]
    public function transportNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $transportType = trim($request->request->get('transportType', ''));
            $companyName = trim($request->request->get('companyName', ''));
            
            if (!$transportType || !$companyName) {
                $this->addFlash('error', 'Le type et la compagnie sont obligatoires.');
                return $this->render('admin/transport_form.html.twig', ['transport' => null]);
            }
            
            $t = new TransportOffer();
            $this->fillTransport($t, $request);
            $em->persist($t);
            $em->flush();
            $this->addFlash('success', '✅ Transport « ' . $t->getCompanyName() . ' » créé !');
            return $this->redirectToRoute('admin_transports');
        }
        return $this->render('admin/transport_form.html.twig', ['transport' => null]);
    }

    #[Route('/transport/{id}/edit', name: 'admin_transport_edit', methods: ['GET', 'POST'])]
    public function transportEdit(int $id, Request $request, TransportOfferRepository $repo, EntityManagerInterface $em): Response
    {
        $t = $repo->find($id);
        if (!$t) throw $this->createNotFoundException();
        
        if ($request->isMethod('POST')) {
            $transportType = trim($request->request->get('transportType', ''));
            $companyName = trim($request->request->get('companyName', ''));
            
            if (!$transportType || !$companyName) {
                $this->addFlash('error', 'Le type et la compagnie sont obligatoires.');
                return $this->render('admin/transport_form.html.twig', ['transport' => $t]);
            }
            
            $this->fillTransport($t, $request);
            $em->flush();
            $this->addFlash('success', '✅ Transport mis à jour !');
            return $this->redirectToRoute('admin_transports');
        }
        return $this->render('admin/transport_form.html.twig', ['transport' => $t]);
    }

    #[Route('/transport/{id}/delete', name: 'admin_transport_delete', methods: ['POST'])]
    public function transportDelete(int $id, TransportOfferRepository $repo, EntityManagerInterface $em): Response
    {
        $t = $repo->find($id);
        if ($t) { $em->remove($t); $em->flush(); }
        $this->addFlash('success', 'Transport supprimé.');
        return $this->redirectToRoute('admin_transports');
    }

    private function fillTransport(TransportOffer $t, Request $request): void
    {
        $t->setTransportType(trim($request->request->get('transportType', '')))
          ->setCompanyName(trim($request->request->get('companyName', '')))
          ->setDepartureCity(trim($request->request->get('departureCity', '')))
          ->setArrivalCity(trim($request->request->get('arrivalCity', '')))
          ->setDepartureStation($request->request->get('departureStation') ?: null)
          ->setArrivalStation($request->request->get('arrivalStation') ?: null)
          ->setDuration($request->request->get('duration') ?: null)
          ->setAvailableSeats($request->request->get('availableSeats') ? (int)$request->request->get('availableSeats') : null)
          ->setPrice((float)$request->request->get('price', 0))
          ->setAmenities($request->request->get('amenities') ?: null)
          ->setIsActive((bool)$request->request->get('isActive', true));

        $departureDatetime = $request->request->get('departureDatetime');
        if ($departureDatetime) {
            $t->setDepartureDatetime(new \DateTime($departureDatetime));
        }

        $arrivalDatetime = $request->request->get('arrivalDatetime');
        if ($arrivalDatetime) {
            $t->setArrivalDatetime(new \DateTime($arrivalDatetime));
        }

        $file = $request->files->get('image');
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) && $file->getSize() <= 5 * 1024 * 1024) {
                $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/transports';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $filename = uniqid('trans_') . '.' . $ext;
                $file->move($dir, $filename);
                $t->setImagePath('uploads/transports/' . $filename);
            }
        }
    }

    /* ══════════════════════ RÉSERVATIONS TRANSPORTS ══════════════════════ */
    #[Route('/reservations-transports', name: 'admin_res_transports')]
    public function resTransports(Request $request, TransportBookingRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $statut = $request->query->get('statut', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('b')
            ->leftJoin('b.transportOffer', 't')
            ->addSelect('t');

        if ($q) {
            $qb->andWhere('b.customerName LIKE :q OR b.customerEmail LIKE :q OR t.companyName LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($statut) {
            $qb->andWhere('b.status = :statut')->setParameter('statut', $statut);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('b.id', 'ASC'),
            'price_asc' => $qb->orderBy('b.totalPrice', 'ASC'),
            'price_desc' => $qb->orderBy('b.totalPrice', 'DESC'),
            default => $qb->orderBy('b.id', 'DESC'),
        };

        $reservations = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10);

        return $this->render('admin/res_transports.html.twig', ['reservations' => $reservations]);
    }

    #[Route('/reservation-transport/{id}/statut', name: 'admin_res_transport_statut', methods: ['POST'])]
    public function resTransportStatut(int $id, Request $request, TransportBookingRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $booking = $repo->find($id);
        if (!$booking) return new JsonResponse(['error' => 'Not found'], 404);
        
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED'])) {
            $booking->setStatus($newStatus);
            $em->flush();
            return new JsonResponse(['success' => true, 'status' => $newStatus]);
        }
        return new JsonResponse(['error' => 'Invalid status'], 400);
    }

    #[Route('/reservation-transport/{id}/delete', name: 'admin_res_transport_delete', methods: ['POST'])]
    public function resTransportDelete(int $id, TransportBookingRepository $repo, EntityManagerInterface $em): Response
    {
        $booking = $repo->find($id);
        if ($booking) { $em->remove($booking); $em->flush(); }
        $this->addFlash('success', 'Réservation transport supprimée.');
        return $this->redirectToRoute('admin_res_transports');
    }

    /* ══════════════════════ AVIS TRANSPORTS ══════════════════════ */
    #[Route('/avis-transports', name: 'admin_avis_transports')]
    public function avisTransports(Request $request, TransportAvisRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        
        $qb = $repo->createQueryBuilder('a')
            ->leftJoin('a.transportOffer', 't')
            ->addSelect('t');

        if ($q) {
            $qb->andWhere('a.author LIKE :q OR a.comment LIKE :q OR t.companyName LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('a.id', 'DESC');
        $avis = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10);

        return $this->render('admin/avis_transports.html.twig', ['avis' => $avis]);
    }

    #[Route('/avis-transport/{id}/delete', name: 'admin_avis_transport_delete', methods: ['POST'])]
    public function avisTransportDelete(int $id, TransportAvisRepository $repo, EntityManagerInterface $em): Response
    {
        $avis = $repo->find($id);
        if ($avis) { $em->remove($avis); $em->flush(); }
        $this->addFlash('success', 'Avis transport supprimé.');
        return $this->redirectToRoute('admin_avis_transports');
    }

    /* ══════════════════════ AVIS CIRCUITS ══════════════════════ */
    #[Route('/avis-circuits', name: 'admin_avis_circuits')]
    public function avisCircuits(Request $request, CircuitAvisRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        
        $qb = $repo->createQueryBuilder('a')
            ->leftJoin('a.circuit', 'c')
            ->addSelect('c');

        if ($q) {
            $qb->andWhere('a.author LIKE :q OR a.comment LIKE :q OR c.titre LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('a.id', 'DESC');
        $avis = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10);

        return $this->render('admin/avis_circuits.html.twig', ['avis' => $avis]);
    }

    #[Route('/avis-circuit/{id}/delete', name: 'admin_avis_circuit_delete', methods: ['POST'])]
    public function avisCircuitDelete(int $id, CircuitAvisRepository $repo, EntityManagerInterface $em): Response
    {
        $avis = $repo->find($id);
        if ($avis) { $em->remove($avis); $em->flush(); }
        $this->addFlash('success', 'Avis circuit supprimé.');
        return $this->redirectToRoute('admin_avis_circuits');
    }

    /* ══════════════════════ RÉSERVATIONS CIRCUIT ══════════════════════ */
    #[Route('/reservations-circuits', name: 'admin_res_circuits')]
    public function resCircuits(Request $request, ReservationCircuitRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $statut = $request->query->get('statut', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.circuit', 'c');

        if ($q) {
            $qb->andWhere('r.nomClient LIKE :q OR c.titre LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($statut) {
            $qb->andWhere('r.statut = :statut')->setParameter('statut', $statut);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('r.id', 'ASC'),
            default => $qb->orderBy('r.id', 'DESC'),
        };

        $reservations = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, );

        return $this->render('admin/res_circuits.html.twig', ['reservations' => $reservations]);
    }

    #[Route('/res-circuit/{id}/statut', name: 'admin_res_circuit_statut', methods: ['POST'])]
    public function resCircuitStatut(int $id, Request $request, ReservationCircuitRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $r = $repo->find($id);
        if (!$r) return new JsonResponse(['error' => 'Not found'], 404);
        $r->setStatut($request->request->get('statut', 'EN_ATTENTE'));
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/res-circuit/{id}/delete', name: 'admin_res_circuit_delete', methods: ['POST'])]
    public function resCircuitDelete(int $id, ReservationCircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $r = $repo->find($id);
        if ($r) { $em->remove($r); $em->flush(); }
        $this->addFlash('success', 'Réservation circuit supprimée.');
        return $this->redirectToRoute('admin_res_circuits');
    }

    /* ══════════════════════ ACTIVITÉS ══════════════════════ */
    #[Route('/activites', name: 'admin_activities')]
    public function activities(Request $request, ActivityRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $lieu = $request->query->get('lieu', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('a');

        if ($q) {
            $qb->andWhere('a.title LIKE :q OR a.description LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($lieu) {
            $qb->andWhere('a.lieu = :lieu')->setParameter('lieu', $lieu);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('a.id', 'ASC'),
            'price_asc' => $qb->orderBy('a.price', 'ASC'),
            'price_desc' => $qb->orderBy('a.price', 'DESC'),
            default => $qb->orderBy('a.id', 'DESC'),
        };

        $activities = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, );

        return $this->render('admin/activities.html.twig', ['activities' => $activities]);
    }

    #[Route('/activite/new', name: 'admin_activity_new', methods: ['GET', 'POST'])]
    public function activityNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $price = $request->request->get('price', '');
            
            if (!$title) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->render('admin/activity_form.html.twig', ['activity' => null]);
            }
            if ($price === '' || (float)$price < 0) {
                $this->addFlash('error', 'Le prix doit être valide.');
                return $this->render('admin/activity_form.html.twig', ['activity' => null]);
            }
            
            $file = $request->files->get('image');
            if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/activity_form.html.twig', ['activity' => null]);
            }
            
            $a = new Activity();
            $this->fillActivity($a, $request);
            $em->persist($a);
            $em->flush();
            $this->addFlash('success', '✅ Activité créée !');
            return $this->redirectToRoute('admin_activities');
        }
        return $this->render('admin/activity_form.html.twig', ['activity' => null]);
    }

    #[Route('/activite/{id}/edit', name: 'admin_activity_edit', methods: ['GET', 'POST'])]
    public function activityEdit(int $id, Request $request, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $a = $repo->find($id);
        if (!$a) throw $this->createNotFoundException();
        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $price = $request->request->get('price', '');
            
            if (!$title) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->render('admin/activity_form.html.twig', ['activity' => $a]);
            }
            if ($price === '' || (float)$price < 0) {
                $this->addFlash('error', 'Le prix doit être valide.');
                return $this->render('admin/activity_form.html.twig', ['activity' => $a]);
            }
            
            $file = $request->files->get('image');
            if (!$a->getImage() && (!$file || $file->getError() !== UPLOAD_ERR_OK)) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/activity_form.html.twig', ['activity' => $a]);
            }
            
            $this->fillActivity($a, $request);
            $em->flush();
            $this->addFlash('success', '✅ Activité mise à jour !');
            return $this->redirectToRoute('admin_activities');
        }
        return $this->render('admin/activity_form.html.twig', ['activity' => $a]);
    }

    #[Route('/activite/{id}/delete', name: 'admin_activity_delete', methods: ['POST'])]
    public function activityDelete(int $id, ActivityRepository $repo, EntityManagerInterface $em): Response
    {
        $a = $repo->find($id);
        if ($a) { $em->remove($a); $em->flush(); }
        $this->addFlash('success', 'Activité supprimée.');
        return $this->redirectToRoute('admin_activities');
    }

    private function fillActivity(Activity $a, Request $request): void
    {
        $a->setTitle(trim($request->request->get('title', '')))
          ->setDescription($request->request->get('description') ?: null)
          ->setLieu(trim($request->request->get('lieu', '')))
          ->setPrice((float)$request->request->get('price', 0))
          ->setDuration($request->request->get('duration') ?: null)
          ->setCategory($request->request->get('category') ?: null)
          ->setInclus($request->request->get('inclus') ?: null)
          ->setPointsForts($request->request->get('points_forts') ?: null)
          ->setDisponible((bool)$request->request->get('disponible', true));

        $file = $request->files->get('image');
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) && $file->getSize() <= 5 * 1024 * 1024) {
                $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/activities';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $filename = uniqid('act_') . '.' . $ext;
                $file->move($dir, $filename);
                $a->setImage('uploads/activities/' . $filename);
            }
        }
    }

    /* ══════════════════════ FAQ CHATBOT ══════════════════════ */
    #[Route('/faq', name: 'admin_faq')]
    public function faq(Request $request, \App\Repository\FAQRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $qb = $repo->createQueryBuilder('f');

        if ($q) {
            $qb->andWhere('f.question LIKE :q OR f.keywords LIKE :q OR f.answer LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('f.id', 'DESC');
        $faqs = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10);

        return $this->render('admin/faq.html.twig', [
            'faqs' => $faqs,
            'chatbotAnalytics' => $this->getChatbotAnalytics()
        ]);
    }

    #[Route('/faq/new', name: 'admin_faq_new', methods: ['GET', 'POST'])]
    public function faqNew(Request $request, EntityManagerInterface $em, CacheInterface $cache, \App\Service\OpenAIService $openAIService, \App\Service\HuggingFaceService $hfService, \App\Service\FrenchSpellChecker $spellChecker): Response
    {
        if ($request->isMethod('POST')) {
            $question = trim($request->request->get('question', ''));
            $answer = trim($request->request->get('answer', ''));
            $keywords = trim($request->request->get('keywords', ''));
            
            if (!$question || !$answer) {
                $this->addFlash('error', 'La question et la réponse sont obligatoires.');
                return $this->render('admin/faq_form.html.twig', ['faq' => null]);
            }
            
            // Check and correct spelling
            $questionResult = $spellChecker->checkAndCorrect($question);
            $answerResult = $spellChecker->checkAndCorrect($answer);
            $keywordsResult = $spellChecker->checkAndCorrect($keywords);
            
            // Apply corrections
            $question = $questionResult['corrected'];
            $answer = $answerResult['corrected'];
            $keywords = $keywordsResult['corrected'];
            
            // Show correction notice
            $corrections = [];
            if ($questionResult['hasChanges']) {
                $corrections[] = 'Question corrigée';
            }
            if ($answerResult['hasChanges']) {
                $corrections[] = 'Réponse corrigée';
            }
            if ($keywordsResult['hasChanges']) {
                $corrections[] = 'Mots-clés corrigés';
            }
            
            $faq = new \App\Entity\FAQ();
            $faq->setQuestion($question)
                ->setAnswer($answer)
                ->setKeywords($keywords);
            
            // Generate embedding (OpenAI first, then HF)
            $embedding = null;
            if ($openAIService->isEnabled()) {
                $embedding = $openAIService->generateEmbedding($question . ' ' . $keywords);
            } else if ($hfService->isEnabled()) {
                $embedding = $hfService->generateEmbedding($question . ' ' . $keywords);
            }

            if ($embedding) {
                $faq->setEmbedding($embedding);
            }
            
            $em->persist($faq);
            $em->flush();
            $cache->delete('chatbot_db_faqs');
            
            $msg = $embedding ? '✅ FAQ ajoutée avec embedding !' : '✅ FAQ ajoutée (sans embedding).';
            if (!empty($corrections)) {
                $msg .= ' [Orthographe corrigée]';
            }
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('admin_faq');
        }
        return $this->render('admin/faq_form.html.twig', ['faq' => null]);
    }

    #[Route('/faq/{id}/edit', name: 'admin_faq_edit', methods: ['GET', 'POST'])]
    public function faqEdit(int $id, Request $request, \App\Repository\FAQRepository $repo, EntityManagerInterface $em, CacheInterface $cache, \App\Service\OpenAIService $openAIService, \App\Service\HuggingFaceService $hfService, \App\Service\FrenchSpellChecker $spellChecker): Response
    {
        $faq = $repo->find($id);
        if (!$faq) throw $this->createNotFoundException();
        
        if ($request->isMethod('POST')) {
            $question = trim($request->request->get('question', ''));
            $answer = trim($request->request->get('answer', ''));
            $keywords = trim($request->request->get('keywords', ''));
            
            if (!$question || !$answer) {
                $this->addFlash('error', 'La question et la réponse sont obligatoires.');
                return $this->render('admin/faq_form.html.twig', ['faq' => $faq]);
            }
            
            // Check and correct spelling
            $questionResult = $spellChecker->checkAndCorrect($question);
            $answerResult = $spellChecker->checkAndCorrect($answer);
            $keywordsResult = $spellChecker->checkAndCorrect($keywords);
            
            $question = $questionResult['corrected'];
            $answer = $answerResult['corrected'];
            $keywords = $keywordsResult['corrected'];
            
            $corrections = [];
            if ($questionResult['hasChanges']) $corrections[] = 'Question';
            if ($answerResult['hasChanges']) $corrections[] = 'Réponse';
            if ($keywordsResult['hasChanges']) $corrections[] = 'Mots-clés';
            
            $faq->setQuestion($question)
                ->setAnswer($answer)
                ->setKeywords($keywords);
            
            // Re-generate embedding if question or keywords changed
            $embedding = null;
            if ($openAIService->isEnabled()) {
                $embedding = $openAIService->generateEmbedding($question . ' ' . $keywords);
            } else if ($hfService->isEnabled()) {
                $embedding = $hfService->generateEmbedding($question . ' ' . $keywords);
            }

            if ($embedding) {
                $faq->setEmbedding($embedding);
            }
            
            $em->flush();
            $cache->delete('chatbot_db_faqs');
            
            $msg = $embedding ? '✅ FAQ mise à jour avec embedding !' : '✅ FAQ mise à jour (sans embedding).';
            if (!empty($corrections)) {
                $msg .= ' [Orthographe corrigée: ' . implode(', ', $corrections) . ']';
            }
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('admin_faq');
        }
        return $this->render('admin/faq_form.html.twig', ['faq' => $faq]);
    }

    #[Route('/faq/{id}/delete', name: 'admin_faq_delete', methods: ['POST'])]
    public function faqDelete(int $id, \App\Repository\FAQRepository $repo, EntityManagerInterface $em, CacheInterface $cache): Response
    {
        $faq = $repo->find($id);
        if ($faq) { $em->remove($faq); $em->flush(); }
$cache->delete('chatbot_db_faqs');
        $this->addFlash('success', 'FAQ supprimée.');
        return $this->redirectToRoute('admin_faq');
    }
    
    #[Route('/faq/spellcheck', name: 'admin_faq_spellcheck', methods: ['POST'])]
    public function faqSpellCheck(Request $request, \App\Service\FrenchSpellChecker $spellChecker): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $question = $data['question'] ?? '';
        $answer = $data['answer'] ?? '';
        $keywords = $data['keywords'] ?? '';
        
        $changes = [];
        
        // Check question
        $qResult = $spellChecker->checkAndCorrect($question);
        if ($qResult['hasChanges']) {
            $changes[] = [
                'field' => 'question',
                'original' => $qResult['original'],
                'corrected' => $qResult['corrected']
            ];
        }
        
        // Check answer
        $aResult = $spellChecker->checkAndCorrect($answer);
        if ($aResult['hasChanges']) {
            $changes[] = [
                'field' => 'answer',
                'original' => $aResult['original'],
                'corrected' => $aResult['corrected']
            ];
        }
        
        // Check keywords
        if ($keywords) {
            $kResult = $spellChecker->checkAndCorrect($keywords);
            if ($kResult['hasChanges']) {
                $changes[] = [
                    'field' => 'keywords',
                    'original' => $kResult['original'],
                    'corrected' => $kResult['corrected']
                ];
            }
        }
        
        return $this->json([
            'hasChanges' => !empty($changes),
            'changes' => $changes
        ]);
    }
    
    #[Route('/faq/autocomplete', name: 'admin_faq_autocomplete')]
    public function faqAutocomplete(Request $request, \App\Repository\FAQRepository $repo): JsonResponse
    {
        $q = $request->query->get('q', '');
        $excludeId = $request->query->getInt('exclude', 0);
        
        if (strlen($q) < 2) {
            return $this->json([]);
        }
        
        $searchTerm = '%' . $q . '%';
        
        $faqs = $repo->createQueryBuilder('f')
            ->where('f.question LIKE :q')
            ->orWhere('f.keywords LIKE :q')
            ->orWhere('f.answer LIKE :q')
            ->setParameter('q', $searchTerm)
            ->setMaxResults(15)
            ->orderBy('LENGTH(f.question)', 'ASC')
            ->getQuery()->getResult();
        
        $results = [];
        foreach ($faqs as $faq) {
            if ($faq->getId() === $excludeId) continue;
            $results[] = [
                'id' => $faq->getId(),
                'question' => $faq->getQuestion(),
                'answer' => $faq->getAnswer(),
                'keywords' => $faq->getKeywords(),
            ];
        }
        
        return $this->json($results);
    }

    #[Route('/faq/ai-suggest', name: 'admin_faq_ai_suggest')]
    public function faqAiSuggest(Request $request, \App\Service\OpenAIService $openAIService): JsonResponse
    {
        $question = $request->query->get('q', '');
        
        if (strlen($question) < 3) {
            return $this->json(['suggestions' => []]);
        }
        
        if (!$openAIService->isEnabled()) {
            return $this->json(['error' => 'OpenAI non configuré', 'suggestions' => []]);
        }
        
        $prompt = <<<PROMPT
Tu es un assistant qui aide à créer des FAQs pour un site de voyage en Tunisie (Fly&Go).
Pour la question: "$question"

Génère une réponse appropriée et une suggestion de question améliorée si nécessaire.

Format JSON obligatoire:
{"suggestion": "Question améliorée ou相同的question", "answer": "Réponse détaillée et utile en français"}

Génère seulement le JSON, rien d'autre.
PROMPT;
        
        $result = $openAIService->chat($prompt, [], null);
        
        if ($result['success']) {
            $content = $result['response'];
            $content = trim($content);
            if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                $data = json_decode($matches[0], true);
                if (is_array($data) && !empty($data['suggestion'])) {
                    return $this->json([
                        'suggestions' => [[
                            'question' => $data['suggestion'],
                            'answer' => $data['answer'] ?? ''
                        ]]
                    ]);
                }
            }
        }
        
        return $this->json(['suggestions' => []]);
    }

    #[Route('/faq/ai-generate', name: 'admin_faq_ai_generate')]
    public function faqAiGenerate(Request $request, \App\Service\OpenAIService $openAIService): JsonResponse
    {
        $apiKey = $openAIService->getApiKey();
        if (empty($apiKey)) {
            return $this->json(['error' => 'Clé API OpenAI manquante', 'answer' => '', 'keywords' => '']);
        }
        
        $question = $request->query->get('q', '');
        
        if (strlen($question) < 3) {
            return $this->json(['answer' => '', 'keywords' => '', 'error' => 'Question trop courte']);
        }
        
        if (!$openAIService->isEnabled()) {
            return $this->json(['error' => 'OpenAI non configuré. Vérifiez la clé API dans le fichier .env', 'answer' => '', 'keywords' => '']);
        }
        
        $prompt = <<<PROMPT
Tu es un assistant expert du service client pour Fly&Go, un site de voyage en Tunisie.
Question: "$question"

Génère une réponse professionnelle et utile en français (2-4 phrases).

Format JSON exactement:
{"answer": "Votre réponse ici", "keywords": "mot1, mot2, mot3"}

Réponds uniquement en JSON, rien d'autre.
PROMPT;
        
        $result = $openAIService->chat($prompt, [], null);
        
        if (!$result['success']) {
            return $this->json(['error' => $result['error'] ?? 'Erreur OpenAI', 'answer' => '', 'keywords' => '']);
        }
        
        $content = $result['response'];
        $content = trim($content);
        
        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $data = json_decode($matches[0], true);
            if (is_array($data) && !empty($data['answer'])) {
                return $this->json([
                    'answer' => $data['answer'],
                    'keywords' => $data['keywords'] ?? ''
                ]);
            }
        }
        
        return $this->json(['answer' => '', 'keywords' => '', 'error' => 'Réponse invalide']);
    }

    #[Route('/faq/sync-embeddings', name: 'admin_faq_sync_embeddings')]
    public function faqSyncEmbeddings(\App\Repository\FAQRepository $repo, EntityManagerInterface $em, \App\Service\OpenAIService $openAIService, \App\Service\HuggingFaceService $hfService): Response
    {
        // Increase time limit for this long-running task
        set_time_limit(300); // 5 minutes

        $localEmbeddingService = new \App\Service\LocalEmbeddingService();

        // Fetch only FAQs that don't have embeddings, limited to 50 per batch to avoid timeouts
        $faqs = $repo->createQueryBuilder('f')
            ->where('f.embedding IS NULL')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        if (empty($faqs)) {
            $this->addFlash('info', "Toutes les FAQ sont déjà synchronisées.");
            return $this->redirectToRoute('admin_faq');
        }

        $count = 0;
        $errors = [];
        $servicesTried = [];

        foreach ($faqs as $faq) {
            $embedding = null;
            $textToEmbed = $faq->getQuestion() . ' ' . ($faq->getKeywords() ?: '');
            
            try {
                if ($openAIService->isEnabled()) {
                    $servicesTried['OpenAI'] = true;
                    $embedding = $openAIService->generateEmbedding($textToEmbed);
                }
                
                if (!$embedding && $hfService->isEnabled()) {
                    $servicesTried['HuggingFace'] = true;
                    $embedding = $hfService->generateEmbedding($textToEmbed);
                }
                
                if (!$embedding) {
                    $servicesTried['Local'] = true;
                    $embedding = $localEmbeddingService->generateEmbedding($textToEmbed);
                }
                
                if (!$embedding) {
                    $errors[] = "Tous les services ont échoué.";
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if ($embedding) {
                $faq->setEmbedding($embedding);
                $count++;
            }
        }
        
        $em->flush();

        if ($count > 0) {
            $this->addFlash('success', "✅ $count embeddings générés avec succès !");
        } else {
            $errorMsg = "⚠️ Échec de la génération.";
            if (!empty($servicesTried)) {
                $errorMsg .= " Services tentés : " . implode(', ', array_keys($servicesTried)) . ".";
            }
            if (!empty($errors)) {
                $errorMsg .= " Détails : " . implode(' | ', array_unique($errors));
            }
            $this->addFlash('warning', $errorMsg);
        }

        return $this->redirectToRoute('admin_faq');
    }

    /* ══════════════════════ BOOKINGS ACTIVITÉ ══════════════════════ */
    #[Route('/bookings', name: 'admin_bookings')]
    public function bookings(Request $request, BookingRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $status = $request->query->get('status', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('b')
            ->leftJoin('b.activity', 'a');

        if ($q) {
            $qb->andWhere('b.customerName LIKE :q OR b.email LIKE :q OR a.title LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($status) {
            $qb->andWhere('b.status = :status')->setParameter('status', $status);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('b.id', 'ASC'),
            default => $qb->orderBy('b.id', 'DESC'),
        };

        $bookings = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, );

        return $this->render('admin/bookings.html.twig', ['bookings' => $bookings]);
    }

    #[Route('/booking/{id}/statut', name: 'admin_booking_statut', methods: ['POST'])]
    public function bookingStatut(int $id, Request $request, BookingRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $b = $repo->find($id);
        if (!$b) return new JsonResponse(['error' => 'Not found'], 404);
        $b->setStatus($request->request->get('status', 'PENDING'));
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/booking/{id}/delete', name: 'admin_booking_delete', methods: ['POST'])]
    public function bookingDelete(int $id, BookingRepository $repo, EntityManagerInterface $em): Response
    {
        $b = $repo->find($id);
        if ($b) { $em->remove($b); $em->flush(); }
        $this->addFlash('success', 'Réservation supprimée.');
        return $this->redirectToRoute('admin_bookings');
    }

    /* ══════════════════════ REVIEWS ══════════════════════ */
    #[Route('/reviews', name: 'admin_reviews')]
    public function reviews(Request $request, ReviewRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');

        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.activity', 'a');

        if ($q) {
            $qb->andWhere('r.author LIKE :q OR r.comment LIKE :q OR a.title LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        $qb->orderBy('r.createdAt', 'DESC');

        $reviews = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 12, );

        return $this->render('admin/reviews.html.twig', ['reviews' => $reviews]);
    }

    #[Route('/review/{id}/delete', name: 'admin_review_delete', methods: ['POST'])]
    public function reviewDelete(int $id, ReviewRepository $repo, EntityManagerInterface $em): Response
    {
        $r = $repo->find($id);
        if ($r) { $em->remove($r); $em->flush(); }
        $this->addFlash('success', 'Avis supprimé.');
        return $this->redirectToRoute('admin_reviews');
    }

    /* ══════════════════════ FORUM ══════════════════════ */
    #[Route('/forum', name: 'admin_forum')]
    public function forum(Request $request, ForumPostRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q', '');
        $status = $request->query->get('status', '');
        $sort = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('p');

        if ($q) {
            $qb->andWhere('p.title LIKE :q OR p.author LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($status) {
            $qb->andWhere('p.status = :status')->setParameter('status', $status);
        }

        match ($sort) {
            'oldest' => $qb->orderBy('p.createdAt', 'ASC'),
            default => $qb->orderBy('p.createdAt', 'DESC'),
        };

        $posts = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, );

        return $this->render('admin/forum.html.twig', ['posts' => $posts]);
    }

    #[Route('/forum/{id}/moderate', name: 'admin_forum_moderate', methods: ['POST'])]
    public function moderatePost(int $id, Request $request, ForumPostRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $post = $repo->find($id);
        if (!$post) return new JsonResponse(['error' => 'Not found'], 404);
        $post->setStatus($request->request->get('status', 'APPROVED'));
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/forum/{id}/delete', name: 'admin_forum_delete', methods: ['POST'])]
    public function forumDelete(int $id, ForumPostRepository $repo, EntityManagerInterface $em): Response
    {
        $p = $repo->find($id);
        if ($p) { $em->remove($p); $em->flush(); }
        $this->addFlash('success', 'Post supprimé.');
        return $this->redirectToRoute('admin_forum');
    }

    /* ══════════════════════ STATS AJAX ══════════════════════ */
    #[Route('/stats/revenus', name: 'admin_stats_revenus')]
    public function statsRevenus(ReservationRepository $repo): JsonResponse
    {
        $data = $repo->getRevenusParMois();
        $mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
        $labels = [];
        $values = [];
        foreach ($data as $d) {
            $labels[] = $mois[(int)$d['mois'] - 1] . ' ' . $d['annee'];
            $values[] = (float)$d['total'];
        }
        return new JsonResponse(['labels' => $labels, 'values' => $values]);
    }

    #[Route('/stats/villes', name: 'admin_stats_villes')]
    public function statsVilles(ReservationRepository $repo): JsonResponse
    {
        $data = $repo->getTauxOccupationParVille();
        return new JsonResponse([
            'labels' => array_column($data, 'ville'),
            'values' => array_column($data, 'total'),
        ]);
    }

    /* ══════════════════════ PDF EXPORT ══════════════════════ */
    #[Route('/export/pdf', name: 'admin_export_pdf')]
    public function exportPdf(
        HebergementRepository $hebRepo,
        ReservationRepository $resRepo,
        AvisRepository        $avisRepo
    ): Response {
        $reservations = $resRepo->findBy([], ['id' => 'DESC']);
        $revenus = array_sum(array_map(fn($r) => $r->getMontantTotal(), $reservations));

        $html = $this->renderView('admin/pdf_stats.html.twig', [
            'totalHebergements' => $hebRepo->count([]),
            'totalReservations' => count($reservations),
            'revenus'           => $revenus,
            'moyenneAvis'       => $avisRepo->getMoyenneGenerale(),
            'reservations'      => array_slice($reservations, 0, 20),
            'date'              => new \DateTime(),
        ]);

        // Simple HTML response for PDF (use Dompdf if installed)
        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return new Response($dompdf->output(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="flyandgo_stats.pdf"',
            ]);
        }

        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }

    /* ══════════════════════ CALENDAR EVENTS ══════════════════════ */
    #[Route('/calendar', name: 'admin_calendar')]
    public function calendar(CalendarEventRepository $repo, ReservationCircuitRepository $rcRepo): Response
    {
        $events = $repo->findAll();
        $colors = ['#3788d8', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        
        return $this->render('admin/calendar.html.twig', [
            'events' => $events,
            'colors' => $colors,
            'reservationEvents' => $this->getCalendarEvents($rcRepo),
        ]);
    }

    #[Route('/calendar/events', name: 'admin_calendar_events')]
    public function calendarEvents(Request $request, CalendarEventRepository $repo): JsonResponse
    {
        $events = $repo->findAll();
        $result = [];
        
        foreach ($events as $e) {
            $result[] = [
                'id' => $e->getId(),
                'title' => $e->getTitle(),
                'start' => $e->getStartDate()->format('c'),
                'end' => $e->getEndDate()?->format('c'),
                'allDay' => $e->isAllDay(),
                'backgroundColor' => $e->getColor(),
                'borderColor' => $e->getColor(),
            ];
        }
        
        return $this->json($result);
    }

    #[Route('/calendar/new', name: 'admin_calendar_new', methods: ['POST'])]
    public function calendarNew(Request $request, CalendarEventRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $title = $request->request->get('title', '');
        $description = $request->request->get('description', '');
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $allDay = $request->request->getBoolean('allDay', false);
        $color = $request->request->get('color', '#3788d8');

        if (!$title || !$startDate) {
            return $this->json(['error' => 'Titre et date de début requis'], 400);
        }

        $event = new CalendarEvent();
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setStartDate(new \DateTime($startDate));
        if ($endDate) {
            $event->setEndDate(new \DateTime($endDate));
        }
        $event->setAllDay($allDay);
        $event->setColor($color);
        $event->setCreatedBy($this->getUser()?->getId());

        $em->persist($event);
        $em->flush();

        return $this->json(['success' => true, 'event' => [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'start' => $event->getStartDate()->format('c'),
            'end' => $event->getEndDate()?->format('c'),
            'allDay' => $event->isAllDay(),
            'color' => $event->getColor(),
        ]]);
    }

    #[Route('/calendar/edit/{id}', name: 'admin_calendar_edit', methods: ['POST'])]
    public function calendarEdit(int $id, Request $request, CalendarEventRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $event = $repo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Événement non trouvé'], 404);
        }

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $allDay = $request->request->getBoolean('allDay');
        $color = $request->request->get('color');

        if ($title) $event->setTitle($title);
        if ($description !== null) $event->setDescription($description);
        if ($startDate) $event->setStartDate(new \DateTime($startDate));
        if ($endDate) {
            $event->setEndDate(new \DateTime($endDate));
        } else {
            $event->setEndDate(null);
        }
        if ($allDay !== null) $event->setAllDay($allDay);
        if ($color) $event->setColor($color);
        
        $event->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['success' => true, 'event' => [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'start' => $event->getStartDate()->format('c'),
            'end' => $event->getEndDate()?->format('c'),
            'allDay' => $event->isAllDay(),
            'color' => $event->getColor(),
        ]]);
    }

    #[Route('/calendar/delete/{id}', name: 'admin_calendar_delete')]
    public function calendarDelete(int $id, CalendarEventRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $event = $repo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Événement non trouvé'], 404);
        }

        $em->remove($event);
        $em->flush();

        return $this->json(['success' => true]);
    }

    /* ══════════════════════ GEOCODING API ══════════════════════ */
    #[Route('/ajax/geocode', name: 'admin_geocode', methods: ['POST'])]
    public function geocode(Request $request, GeocodingService $geocodingService): JsonResponse
    {
        $address = $request->request->get('address', '');
        if (!$address) {
            return $this->json(['error' => 'Address required'], 400);
        }

        $coords = $geocodingService->getCoordinatesWithKey($address);
        
        if ($coords) {
            return $this->json($coords);
        }
        
        return $this->json(['error' => 'Location not found'], 404);
    }
}
