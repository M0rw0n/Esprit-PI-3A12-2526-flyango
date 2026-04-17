<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Avis;
use App\Entity\Circuit;
use App\Entity\ForumPost;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\ReservationCircuit;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\AvisRepository;
use App\Repository\BookingRepository;
use App\Repository\CircuitRepository;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\HebergementRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
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
        AvisRepository           $avisRepo,
        UserRepository           $userRepo
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

        return $this->render('admin/dashboard.html.twig', [
            'totalHebergements'  => $hebRepo->count([]),
            'totalReservations'  => count($reservations) + count($bookings) + count($rcList),
            'totalCircuits'      => $circRepo->count([]),
            'totalActivities'    => $actRepo->count([]),
            'totalUsers'         => $userRepo->count([]),
            'totalForumPosts'    => count($posts),
            'revenus'            => $revenus,
            'moyenneAvis'        => $avisRepo->getMoyenneGenerale(),
            'pendingRes'         => count(array_filter($reservations, fn($r) => $r->getStatut() === 'EN_ATTENTE')),
            'pendingBook'        => count(array_filter($bookings, fn($b) => $b->getStatus() === 'PENDING')),
            'pendingPosts'       => count(array_filter($posts, fn($p) => $p->getStatus() === 'PENDING')),
            'recentReservations' => $resRepo->findBy([], ['id' => 'DESC'], 6),
            'recentBookings'     => $bookRepo->findBy([], ['id' => 'DESC'], 4),
            'recentPosts'        => $forumRepo->findBy([], ['createdAt' => 'DESC'], 4),
            'revenusParMois'     => $revenusParMois,
            'occParVille'        => $occParVille,
            'topHeb'             => $topHeb,
        ]);
    }

    /* ══════════════════════ USERS ══════════════════════ */
    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $repo): Response
    {
        return $this->render('admin/users.html.twig', ['users' => $repo->findAll()]);
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

    /* ══════════════════════ HÉBERGEMENTS ══════════════════════ */
    #[Route('/hebergements', name: 'admin_hebergements')]
    public function hebergements(HebergementRepository $repo): Response
    {
        return $this->render('admin/hebergements.html.twig', ['hebergements' => $repo->findAll()]);
    }

    #[Route('/hebergement/new', name: 'admin_hebergement_new', methods: ['GET', 'POST'])]
    public function hebergementNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
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
        if ($file && $file->isValid()) {
            $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/hebergements';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $filename = uniqid('heb_') . '.' . $file->guessExtension();
            $file->move($dir, $filename);
            $h->setImage('uploads/hebergements/' . $filename);
        }
    }

    /* ══════════════════════ RÉSERVATIONS HÉBERGEMENT ══════════════════════ */
    #[Route('/reservations', name: 'admin_reservations')]
    public function reservations(ReservationRepository $repo): Response
    {
        return $this->render('admin/reservations.html.twig', [
            'reservations' => $repo->findBy([], ['id' => 'DESC']),
        ]);
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
    public function avis(AvisRepository $repo): Response
    {
        return $this->render('admin/avis.html.twig', [
            'avis' => $repo->findBy([], ['createdAt' => 'DESC']),
        ]);
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
    public function circuits(CircuitRepository $repo): Response
    {
        return $this->render('admin/circuits.html.twig', ['circuits' => $repo->findAll()]);
    }

    #[Route('/circuit/new', name: 'admin_circuit_new', methods: ['GET', 'POST'])]
    public function circuitNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
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
          ->setDuree($request->request->get('duree') ?: null)
          ->setPrix((float)$request->request->get('prix', 0))
          ->setDifficulte($request->request->get('difficulte') ?: null)
          ->setPlacesDisponibles($request->request->get('places') ? (int)$request->request->get('places') : null)
          ->setDepart($request->request->get('depart') ?: null)
          ->setDestination($request->request->get('destination') ?: null)
          ->setActif((bool)$request->request->get('actif', true));

        $file = $request->files->get('image');
        if ($file && $file->isValid()) {
            $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/circuits';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $filename = uniqid('circ_') . '.' . $file->guessExtension();
            $file->move($dir, $filename);
            $c->setImage('uploads/circuits/' . $filename);
        }
    }

    /* ══════════════════════ RÉSERVATIONS CIRCUIT ══════════════════════ */
    #[Route('/reservations-circuits', name: 'admin_res_circuits')]
    public function resCircuits(ReservationCircuitRepository $repo): Response
    {
        return $this->render('admin/res_circuits.html.twig', [
            'reservations' => $repo->findBy([], ['id' => 'DESC']),
        ]);
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
    public function activities(ActivityRepository $repo): Response
    {
        return $this->render('admin/activities.html.twig', ['activities' => $repo->findAll()]);
    }

    #[Route('/activite/new', name: 'admin_activity_new', methods: ['GET', 'POST'])]
    public function activityNew(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
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
        if ($file && $file->isValid()) {
            $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/activities';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $filename = uniqid('act_') . '.' . $file->guessExtension();
            $file->move($dir, $filename);
            $a->setImage('uploads/activities/' . $filename);
        }
    }

    /* ══════════════════════ BOOKINGS ACTIVITÉ ══════════════════════ */
    #[Route('/bookings', name: 'admin_bookings')]
    public function bookings(BookingRepository $repo): Response
    {
        return $this->render('admin/bookings.html.twig', [
            'bookings' => $repo->findBy([], ['id' => 'DESC']),
        ]);
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
    public function reviews(ReviewRepository $repo): Response
    {
        return $this->render('admin/reviews.html.twig', ['reviews' => $repo->findBy([], ['createdAt' => 'DESC'])]);
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
    public function forum(ForumPostRepository $repo): Response
    {
        return $this->render('admin/forum.html.twig', [
            'posts' => $repo->findBy([], ['createdAt' => 'DESC']),
        ]);
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
