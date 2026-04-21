<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Avis;
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
use App\Service\ExchangeRateService;
use App\Service\GeoIPService;
use App\Service\AnalyticsService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private ?WeatherService $weatherService = null,
        private ?ExchangeRateService $exchangeRateService = null,
        private ?GeoIPService $geoIPService = null,
        private ?AnalyticsService $analyticsService = null,
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
        UserRepository           $userRepo,
        ProfilVoyageurRepository $profilRepo
    ): Response {
        $reservations = $resRepo->findBy([], ['id' => 'DESC']);
        $bookings     = $bookRepo->findBy([], ['id' => 'DESC']);
        $rcList       = $rcRepo->findBy([], ['id' => 'DESC']);
        $posts        = $forumRepo->findAll();

        $revenus = array_sum(array_map(fn($r) => $r->getMontantTotal(), $reservations))
                 + array_sum(array_map(fn($b) => $b->getTotalPrice(), $bookings))
                 + array_sum(array_map(fn($r) => $r->getMontantTotal(), $rcList));

        $revenusParMois = $resRepo->getRevenusParMois();
        $occParVille    = $resRepo->getTauxOccupationParVille();
        $topHeb         = $resRepo->getTopHebergements();
        $profilStats    = $profilRepo->getStatistics();

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
            'totalHebergements'  => $hebRepo->count([]),
            'totalReservations'  => count($reservations) + count($bookings) + count($rcList),
            'totalCircuits'      => $circRepo->count([]),
            'totalActivities'    => $actRepo->count([]),
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
        ]);
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
    public function hebergementNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom', ''));
            $ville = trim($request->request->get('ville', ''));
            $type = $request->request->get('type', '');
            $prix = $request->request->get('prix_par_nuit', '');
            
            if (!$nom) {
                $this->addFlash('error', 'Le nom est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            if (!$ville) {
                $this->addFlash('error', 'La ville est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            if (!$type) {
                $this->addFlash('error', 'Le type est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            if ($prix === '' || (float)$prix <= 0) {
                $this->addFlash('error', 'Le prix doit être supérieur à 0.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            
            $file = $request->files->get('image');
            if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
            }
            
            $h = new Hebergement();
            $this->fillHebergement($h, $request);
            $em->persist($h);
            $em->flush();
            $this->addFlash('success', '✅ Hébergement « ' . $h->getNom() . ' » créé !');
            return $this->redirectToRoute('admin_hebergements');
        }
        return $this->render('admin/hebergement_form.html.twig', ['hebergement' => null]);
    }

    #[Route('/hebergement/{id}/edit', name: 'admin_hebergement_edit', methods: ['GET', 'POST'])]
    public function hebergementEdit(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $h = $repo->find($id);
        if (!$h) throw $this->createNotFoundException();
        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom', ''));
            $ville = trim($request->request->get('ville', ''));
            $type = $request->request->get('type', '');
            $prix = $request->request->get('prix_par_nuit', '');
            
            if (!$nom) {
                $this->addFlash('error', 'Le nom est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            if (!$ville) {
                $this->addFlash('error', 'La ville est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            if (!$type) {
                $this->addFlash('error', 'Le type est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            if ($prix === '' || (float)$prix <= 0) {
                $this->addFlash('error', 'Le prix doit être supérieur à 0.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            
            $file = $request->files->get('image');
            if (!$h->getImage() && (!$file || $file->getError() !== UPLOAD_ERR_OK)) {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->render('admin/hebergement_form.html.twig', ['hebergement' => $h]);
            }
            
            $this->fillHebergement($h, $request);
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
          ->setDisponible((bool)$request->request->get('disponible', true));

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
        }
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
          ->setPrice((float)$request->request->get('price', 0))
          ->setDuration($request->request->get('duration') ?: null)
          ->setCapacity((int)$request->request->get('capacity', 10))
          ->setLieu($request->request->get('lieu') ?: null)
          ->setActif((bool)$request->request->get('actif', true));

        if ($d = $request->request->get('date')) {
            try { $a->setDate(new \DateTime($d)); } catch (\Exception $e) {}
        }

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
}
