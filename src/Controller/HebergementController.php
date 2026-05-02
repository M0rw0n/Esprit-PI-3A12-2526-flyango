<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\HebergementRepository;
use App\Repository\FavoriteHebergementRepository;
use App\Repository\ReservationRepository;
use App\Service\SentimentService;
use App\Service\MercureNotificationService;
use App\Service\PaymentService;
use App\Service\ContentModerationService;
use App\Service\TravelPreparationAssistantService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HebergementController extends AbstractController
{
    public function __construct(
        private ?SentimentService $sentimentService = null,
        private ?ContentModerationService $moderationService = null,
        private ?TravelPreparationAssistantService $travelAdviceService = null,
        private ?MercureNotificationService $mercureNotificationService = null,
    ) {}

    #[Route('/hebergements', name: 'hebergement_index')]
    public function index(Request $request, HebergementRepository $repo, FavoriteHebergementRepository $favRepo, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $tri = $request->query->get('tri', 'nom');
        $search = $request->query->get('q', '');
        $ville = $request->query->get('ville', '');
        $type = $request->query->get('type', '');
        $prixMin = $request->query->get('prix_min', '');
        $prixMax = $request->query->get('prix_max', '');
        
        $qb = $repo->createQueryBuilder('h');
        
        if ($search) {
            $qb->andWhere('h.nom LIKE :s OR h.description LIKE :s OR h.adresse LIKE :s')
               ->setParameter('s', "%{$search}%");
        }
        
        if ($ville) {
            $qb->andWhere('h.ville = :ville')->setParameter('ville', $ville);
        }
        
        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }
        
        if ($prixMin) {
            $qb->andWhere('h.prixParNuit >= :prixMin')->setParameter('prixMin', (float)$prixMin);
        }
        
        if ($prixMax) {
            $qb->andWhere('h.prixParNuit <= :prixMax')->setParameter('prixMax', (float)$prixMax);
        }
        
        // Sorting
        if ($tri === 'prix_asc') {
            $qb->orderBy('h.prixParNuit', 'ASC');
        } elseif ($tri === 'prix_desc') {
            $qb->orderBy('h.prixParNuit', 'DESC');
        } elseif ($tri === 'popular') {
            $qb->orderBy('h.vues', 'DESC');
        } else {
            $qb->orderBy('h.nom', 'ASC');
        }
        
        $heb = $paginator->paginate($qb->getQuery(), $page, 12);
        
        // Get popular hebergements (most viewed)
        $populaires = $repo->createQueryBuilder('h')
            ->andWhere('h.disponible = 1')
            ->orderBy('h.vues', 'DESC')
            ->setMaxResults(6)
            ->getQuery()->getResult();
        
        $villes = $repo->createQueryBuilder('h')->select('DISTINCT h.ville')->getQuery()->getResult();
        $villes = array_column($villes, 'ville');
        
        $favorites = [];
        if ($this->getUser() && $this->isGranted('ROLE_USER')) {
            $favResults = $favRepo->findBy(['user' => $this->getUser()]);
            $favorites = array_filter(array_map(fn($f) => $f->getHebergement()?->getId(), $favResults));
        }
        
        return $this->render('hebergement/index.html.twig', [
            'hebergements' => $heb,
            'hebergements_populaires' => $populaires,
            'tri' => $tri,
            'search' => $search,
            'q' => $search,
            'ville' => $ville,
            'type' => $type,
            'villes' => $villes,
            'prixMin' => $prixMin,
            'prixMax' => $prixMax,
            'favorites' => $favorites
        ]);
    }

    #[Route('/hebergement/top', name: 'hebergement_top')]
    public function top(HebergementRepository $repo): Response
    {
        $heb = $repo->createQueryBuilder('h')
            ->andWhere('h.disponible = 1')
            ->orderBy('h.vues', 'DESC')
            ->setMaxResults(12)
            ->getQuery()->getResult();
        return $this->render('hebergement/top.html.twig', ['hebergements' => $heb]);
    }

    #[Route('/hebergement/{id}', name: 'hebergement_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, HebergementRepository $repo, FavoriteHebergementRepository $favRepo, EntityManagerInterface $em): Response
    {
        $h = $repo->find($id);
        if (!$h) throw $this->createNotFoundException();
        
        // Increment views
        $h->incrementVues();
        $em->flush();

        $isFavorited = false;
        if ($this->getUser()) {
            $isFavorited = $favRepo->isFavorited($this->getUser(), $h);
        }

        // Generate travel advice
        $travelAdvice = [];
        if ($this->travelAdviceService && $this->travelAdviceService->isEnabled()) {
            $season = $this->getCurrentSeason();
            $accommodationData = [
                'name' => $h->getNom(),
                'city' => $h->getVille(),
                'country' => 'Tunisie',
                'season' => $season,
                'accommodation_type' => $h->getType(),
                'equipment' => $h->getEquipements() ?? [],
                'services' => [],
                'location' => $h->getLocalisation(),
                'near_beach' => $this->isNearBeach($h->getLocalisation()),
                'has_parking' => $this->hasParking($h->getEquipements() ?? []),
                'has_pool' => $this->hasPool($h->getEquipements() ?? []),
            ];
            $result = $this->travelAdviceService->generateAdvice($accommodationData);
            if ($result['success'] ?? false) {
                $travelAdvice = $result['advice'] ?? [];
            }
        }

        return $this->render('hebergement/show.html.twig', [
            'hebergement' => $h,
            'isFavorited' => $isFavorited,
            'travelAdvice' => $travelAdvice,
        ]);
    }

    private function getCurrentSeason(): string
    {
        $month = (int)date('n');
        return match(true) {
            $month >= 3 && $month <= 5 => 'printemps',
            $month >= 6 && $month <= 8 => 'été',
            $month >= 9 && $month <= 11 => 'automne',
            default => 'hiver',
        };
    }

    private function isNearBeach(?string $localisation): bool
    {
        if (!$localisation) return false;
        $localisationLower = mb_strtolower($localisation);
        return str_contains($localisationLower, 'plage') || str_contains($localisationLower, 'bord de mer') || str_contains($localisationLower, 'sea') || str_contains($localisationLower, 'beach');
    }

    private function hasParking(?array $equipements): bool
    {
        if (!$equipements) return false;
        foreach ($equipements as $eq) {
            if (mb_stripos($eq, 'parking') !== false || mb_stripos($eq, 'parking') !== false) {
                return true;
            }
        }
        return false;
    }

    private function hasPool(?array $equipements): bool
    {
        if (!$equipements) return false;
        foreach ($equipements as $eq) {
            if (mb_stripos($eq, 'piscine') !== false || mb_stripos($eq, 'pool') !== false) {
                return true;
            }
        }
        return false;
    }

    #[Route('/hebergement/{id}/avis', name: 'hebergement_avis', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addAvis(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $h = $repo->find($id);
        if (!$h) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $existingAvis = $em->getRepository(Avis::class)->findOneBy(['hebergement' => $h, 'user' => $user]);
        if ($existingAvis) {
            $this->addFlash('error', 'Vous avez déjà laissé un avis pour cet hébergement.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        $commentaire = (string) $request->request->get('commentaire', '');
        
        if ($this->moderationService) {
            $moderationResult = $this->moderationService->analyzeContent($commentaire);
            
            if ($moderationResult['has_offensive']) {
                $this->addFlash('error', 'Votre avis contient des mots inappropriés. Veuillez le reformuler.');
                return $this->redirectToRoute('hebergement_show', ['id' => $id]);
            }
            
            $commentaire = $this->moderationService->maskWords($commentaire);
        }
        
        $avis = new Avis();
        $avis->setHebergement($h)
            ->setUser($user)
            ->setAuteur($user->getFullName())
            ->setNote((int) $request->request->get('note', 5))
            ->setCommentaire($commentaire);

        if ($this->sentimentService && !empty($commentaire)) {
            $analysis = $this->sentimentService->analyze($commentaire);
            $avis->setSentimentFromAnalysis($analysis);
        }

        $em->persist($avis);
        $em->flush();
        $this->mercureNotificationService?->notifyAvisCreated($avis);

        $this->addFlash('success', 'Votre avis a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/hebergement/{id}/reserver', name: 'hebergement_reserver', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function reserver(int $id, Request $request, HebergementRepository $repo, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $h = $repo->find($id);
        if (!$h || !$h->isDisponible()) {
            throw $this->createNotFoundException();
        }

        $nomClient = $request->request->get('nom_client');
        $emailClient = $request->request->get('email_client');
        $telephone = $request->request->get('telephone', '');
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');
        $nbPersonnes = (int) $request->request->get('nb_personnes', 1);
        $total = (float) $request->request->get('montant_total', 0);
        $promoCodeId = $request->request->get('promo_code_id');
        $promoReduction = (float) $request->request->get('promo_reduction', 0);

        if (!$nomClient || !$emailClient || !$dateDebut || !$dateFin) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }
        
        // Validate with Symfony Validator
        $reservation = new Reservation();
        $reservation->setHebergement($h);
        if ($this->getUser()) { $reservation->setUser($this->getUser()); }
        $reservation->setNomClient($nomClient);
        $reservation->setEmailClient($emailClient);
        $reservation->setTelephone($telephone);
        $reservation->setDateDebut(new \DateTime($dateDebut));
        $reservation->setDateFin(new \DateTime($dateFin));
        $reservation->setNombrePersonnes($nbPersonnes);
        $reservation->setNombreChambres((int) ($request->request->get('nb_chambres', 1)));
        $reservation->setMontantTotal($total);
        
        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            $this->addFlash('error', implode('<br>', $errorMessages));
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }
        
        // Check availability
        $blockedDates = $h->getBlockedDates() ?: [];
        $checkIn = new \DateTime($dateDebut);
        $checkOut = new \DateTime($dateFin);
        $nbrChambres = (int) ($request->request->get('nb_chambres', 1));
        
        // Check if dates are blocked
        $conflictDates = [];
        $current = clone $checkIn;
        while ($current < $checkOut) {
            if (in_array($current->format('Y-m-d'), $blockedDates)) {
                $conflictDates[] = $current->format('d/m/Y');
            }
            $current->modify('+1 day');
        }
        
        // Check room availability
        $chambresDispo = $h->getChambresDisponibles() ?: 1;
        if ($nbrChambres > $chambresDispo) {
            $this->addFlash('error', 'Nombre de chambres indisponibles. Maximum: ' . $chambresDispo);
            return $this->redirectToRoute('hebergement_show', ['id' => $id]);
        }

        $reservation->setStatut(Reservation::STATUT_EN_ATTENTE);
        $reservation->setCreatedAt(new \DateTime());

        $em->persist($reservation);
        
        if ($promoCodeId && $promoReduction > 0) {
            $conn = $em->getConnection();
            $conn->executeStatement(
                "UPDATE promo_code SET used_count = used_count + 1 WHERE id = ?",
                [$promoCodeId]
            );
            
            if ($this->getUser()) {
                $conn->insert('promo_code_usage', [
                    'promo_code_id' => $promoCodeId,
                    'user_id' => $this->getUser()->getId(),
                    'reservation_id' => $reservation->getId(),
                    'reduction_amount' => $promoReduction,
                    'used_at' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
            }
        }
        
        $remaining = $h->getCapaciteRestante();
        if ($remaining <= $nbPersonnes && $remaining > 0) {
            $h->setDisponible(false);
        }
        
        $em->flush();

        $this->addFlash('info', 'Réservation créée. Veuillez procéder au paiement pour confirmer.');
        return $this->redirectToRoute('payment_checkout', ['id' => $reservation->getId()]);
    }

    #[Route('/checkout/{id}', name: 'payment_checkout', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function checkout(int $id, ReservationRepository $repo): Response
    {
        $reservation = $repo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation non trouvée');
            return $this->redirectToRoute('hebergement_index');
        }
        return $this->render('payment/checkout.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/checkout/{id}/pay', name: 'payment_pay', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function processPayment(int $id, Request $request, ReservationRepository $repo, PaymentService $paymentService, EntityManagerInterface $em): Response
    {
        $reservation = $repo->find($id);
        
        if (!$reservation) {
            $this->addFlash('error', 'Réservation non trouvée');
            return $this->redirectToRoute('hebergement_index');
        }

        try {
            // Create Stripe checkout session
            $checkoutUrl = $paymentService->createStripeCheckoutSession($reservation);
            
            // Redirect to Stripe checkout page
            return $this->redirect($checkoutUrl);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur paiement Stripe: ' . $e->getMessage());
            return $this->redirectToRoute('payment_checkout', ['id' => $id]);
        }
    }

    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request, ReservationRepository $repo, PaymentService $paymentService, EntityManagerInterface $em): Response
    {
        $reservationId = $request->query->get('reservation');
        $sessionId = $request->query->get('session_id');
        
        if (!$reservationId) {
            $this->addFlash('error', 'Paiement invalide');
            return $this->redirectToRoute('hebergement_index');
        }

        $reservation = $repo->find($reservationId);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation non trouvée');
            return $this->redirectToRoute('hebergement_index');
        }

        // If coming back from Stripe checkout, verify the payment
        if ($sessionId) {
            $verified = $paymentService->verifyStripePayment($sessionId, (int) $reservationId);
            
            if ($verified) {
                $paymentService->processConfirmedPayment($reservation);
                
                // Direct email test
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'from' => 'onboarding@resend.dev',
                    'to' => ['flyandgo.contact@gmail.com'],
                    'subject' => 'Paiement confirmé - FG-' . $reservation->getId(),
                    'html' => '<h1>Paiement confirmé!</h1><p>Réf: FG-' . $reservation->getId() . '</p><p>Montant: ' . $reservation->getMontantTotal() . ' TND</p>'
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer re_gEG5W52G_HCpF1KxGJAvfHtQj7m3oN8d7',
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
                
                $h = $reservation->getHebergement();
                $remaining = $h->getCapaciteRestante();
                if ($remaining <= $reservation->getNombrePersonnes()) {
                    $h->setDisponible(false);
                }
                
                // Reduce available rooms
                $chambresDispo = $h->getChambresDisponibles() ?: 0;
                $nbrChambres = $reservation->getNombreChambres() ?: 1;
                $h->setChambresDisponibles(max(0, $chambresDispo - $nbrChambres));
                
                // Block dates after payment
                $dateDebut = $reservation->getDateDebut()->format('Y-m-d');
                $dateFin = $reservation->getDateFin()->format('Y-m-d');
                $blockedDates = $h->getBlockedDates() ?: [];
                $current = new \DateTime($dateDebut);
                $end = new \DateTime($dateFin);
                while ($current <= $end) {
                    $blockedDates[] = $current->format('Y-m-d');
                    $current->modify('+1 day');
                }
                $h->setBlockedDates($blockedDates);
                $em->flush();
                
                $this->addFlash('success', 'Paiement confirmé ! Vérifiez votre email.');
            } else {
                $this->addFlash('error', 'Paiement non confirmé ou annulé.');
            }
        } else {
            // If no sessionId, it might be a direct access - treat as success if already paid
            if ($reservation->getStatut() === Reservation::STATUT_CONFIRMEE) {
                $this->addFlash('success', 'Réservation déjà confirmée !');
            } else {
                $paymentService->processConfirmedPayment($reservation);
                $this->addFlash('success', 'Réservation créée !');
            }
        }
        
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/reservation/{id}/cancel', name: 'reservation_cancel', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function cancelReservation(int $id, ReservationRepository $repo, PaymentService $paymentService): Response
    {
        $reservation = $repo->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException();
        }

        $paymentService->cancelReservation($reservation);
        $this->addFlash('success', 'Réservation annulée.');
        
        return $this->redirectToRoute('user_reservations');
    }
}
