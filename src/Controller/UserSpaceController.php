<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Entity\CircuitAvis;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\BookingRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\CircuitRepository;
use App\Repository\ReviewRepository;
use App\Repository\CircuitAvisRepository;
=======
use App\Entity\Avis;
use App\Entity\Booking;
use App\Entity\Circuit;
use App\Entity\CircuitAvis;
use App\Entity\Reservation;
use App\Entity\ReservationCircuit;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\BookingRepository;
use App\Repository\CircuitAvisRepository;
use App\Repository\CircuitRepository;
use App\Repository\ReservationCircuitRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\TransportBookingRepository;
>>>>>>> testsisi
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<<<<<<< HEAD
#[Route('/mon-espace')]
class UserSpaceController extends AbstractController
{
    #[Route('', name: 'user_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }

    #[Route('/reservations', name: 'user_reservations', methods: ['GET'])]
    public function reservations(
        Request $request,
        PaginatorInterface $paginator,
        ReservationRepository $resHebRepo,
        BookingRepository $bookingRepo,
        ReservationCircuitRepository $resCircRepo,
    ): Response {
        $user = $this->getUser();

        $hebergements = $paginator->paginate(
            $resHebRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_h', 1),
            5,
            ['pageParameterName' => 'page_h']
        );

        $transports = $paginator->paginate(
            $bookingRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_t', 1),
            5,
            ['pageParameterName' => 'page_t']
        );

        $circuits = $paginator->paginate(
            $resCircRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_c', 1),
            5,
            ['pageParameterName' => 'page_c']
        );

        return $this->render('user/reservations.html.twig', [
            'hebergements' => $hebergements,
            'transports' => $transports,
            'circuits' => $circuits,
        ]);
    }

    #[Route('/avis', name: 'user_avis', methods: ['GET'])]
    public function avis(
        AvisRepository $avisRepo,
        ReviewRepository $reviewRepo,
        CircuitAvisRepository $circuitAvisRepo,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $user = $this->getUser();

        $hebergements = $paginator->paginate(
            $avisRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_h', 1),
            5,
            ['pageParameterName' => 'page_h']
        );

        $activites = $paginator->paginate(
            $reviewRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_a', 1),
            5,
            ['pageParameterName' => 'page_a']
        );

        $circuits = $paginator->paginate(
            $circuitAvisRepo->findBy(['user' => $user], ['id' => 'DESC']),
            $request->query->getInt('page_c', 1),
            5,
            ['pageParameterName' => 'page_c']
        );

        return $this->render('user/avis.html.twig', [
            'hebergements' => $hebergements,
            'activites' => $activites,
            'circuits' => $circuits,
        ]);
    }

    #[Route('/profil', name: 'user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $fullName = $request->request->get('fullName');
            $parts = explode(' ', trim($fullName), 2);
            if (count($parts) === 2) {
                $user->setPrenom($parts[0]);
                $user->setNom($parts[1]);
            } else {
                $user->setPrenom($fullName);
            }
            $user->setTelephone($request->request->get('telephone'));
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
=======
class UserSpaceController extends AbstractController
{
    #[Route('/mon-espace/profil', name: 'user_profile')]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom', ''));
            $prenom = trim($request->request->get('prenom', ''));
            $email = trim($request->request->get('email', ''));
            $telephone = trim($request->request->get('telephone', ''));

            if ($nom) $user->setNom($nom);
            if ($prenom) $user->setPrenom($prenom);
            if ($email) $user->setEmail($email);
            if ($telephone) $user->setTelephone($telephone);

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');
>>>>>>> testsisi
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig');
    }

<<<<<<< HEAD
    #[Route('/avis/hebergement/{id}/delete', name: 'user_hebergement_avis_delete', methods: ['POST'])]
    public function deleteHebergementAvis(int $id, ReviewRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/avis/activity/{id}/delete', name: 'user_activity_avis_delete', methods: ['POST'])]
    public function deleteActivityAvis(int $id, ReviewRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/avis/circuit/{id}/delete', name: 'user_circuit_avis_delete', methods: ['POST'])]
    public function deleteCircuitAvis(int $id, CircuitAvisRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $avis = $repo->find($id);
        if ($avis && $avis->getUser() === $this->getUser()) {
            $em->remove($avis);
            $em->flush();
            $this->addFlash('success', 'Avis supprimé.');
        }
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/circuit/{id}/delete', name: 'user_circuit_delete', methods: ['POST'])]
    public function deleteCircuit(int $id, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $circuit = $repo->find($id);
        if ($circuit && $circuit->getCreator() === $this->getUser()) {
            $em->remove($circuit);
            $em->flush();
            $this->addFlash('success', 'Circuit supprimé.');
        }
        return $this->redirectToRoute('user_dashboard');
    }

    #[Route('/reservation/hebergement/{id}/cancel', name: 'user_hebergement_cancel', methods: ['POST'])]
    public function cancelHebergement(int $id, ReservationRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/reservation/activity/{id}/cancel', name: 'user_activity_cancel', methods: ['POST'])]
    public function cancelActivity(int $id, BookingRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/reservation/circuit/{id}/cancel', name: 'user_circuit_cancel', methods: ['POST'])]
    public function cancelCircuit(int $id, ReservationCircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $res = $repo->find($id);
        if ($res && $res->getUser() === $this->getUser()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }
        return $this->redirectToRoute('user_reservations');
    }
=======
    #[Route('/mon-espace', name: 'user_dashboard')]
    public function dashboard(
        ReservationRepository $reservationRepository,
        BookingRepository $bookingRepository,
        ReservationCircuitRepository $reservationCircuitRepository,
        AvisRepository $avisRepository,
        ReviewRepository $reviewRepository,
        CircuitAvisRepository $circuitAvisRepository,
        CircuitRepository $circuitRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $reservations = $reservationRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $bookings = $bookingRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $circuitReservations = $reservationCircuitRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $avis = $avisRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $reviews = $reviewRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $circuitAvis = $circuitAvisRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $circuits = $circuitRepository->findUserCustomCircuits($user);

        return $this->render('user/dashboard.html.twig', [
            'reservationsCount' => count($reservations) + count($bookings) + count($circuitReservations),
            'avisCount' => count($avis) + count($reviews) + count($circuitAvis),
            'circuitsCount' => count($circuits),
            'latestReservations' => array_slice(array_merge($reservations, $bookings, $circuitReservations), 0, 5),
            'latestAvis' => array_slice(array_merge($avis, $reviews, $circuitAvis), 0, 5),
            'circuits' => array_slice($circuits, 0, 4),
        ]);
    }

    #[Route('/mes-reservations', name: 'user_reservations')]
    public function reservations(
        Request $request,
        PaginatorInterface $paginator,
        ReservationRepository $reservationRepository,
        BookingRepository $bookingRepository,
        ReservationCircuitRepository $reservationCircuitRepository,
        TransportBookingRepository $transportBookingRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $hebQb = $reservationRepository->createQueryBuilder('r')->where('r.user = :user')->setParameter('user', $user)->orderBy('r.createdAt', 'DESC');
        $actQb = $bookingRepository->createQueryBuilder('b')->where('b.user = :user')->setParameter('user', $user)->orderBy('b.id', 'DESC');
        $circQb = $reservationCircuitRepository->createQueryBuilder('c')->where('c.user = :user')->setParameter('user', $user)->orderBy('c.dateReservation', 'DESC');
        $transQb = $transportBookingRepository->createQueryBuilder('t')->where('t.user = :user')->setParameter('user', $user)->orderBy('t.bookingDate', 'DESC');

        $hebPag = $paginator->paginate($hebQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);
        $actPag = $paginator->paginate($actQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);
        $circPag = $paginator->paginate($circQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);
        $transPag = $paginator->paginate($transQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);

        return $this->render('user/reservations.html.twig', [
            'hebergements' => $hebPag,
            'activities' => $actPag,
            'circuits' => $circPag,
            'transports' => $transPag,
        ]);
    }

    #[Route('/mes-avis', name: 'user_avis')]
    public function avis(
        Request $request,
        PaginatorInterface $paginator,
        AvisRepository $avisRepository,
        ReviewRepository $reviewRepository,
        CircuitAvisRepository $circuitAvisRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $hebQb = $avisRepository->createQueryBuilder('a')->where('a.user = :user')->setParameter('user', $user)->orderBy('a.id', 'DESC');
        $actQb = $reviewRepository->createQueryBuilder('r')->where('r.user = :user')->setParameter('user', $user)->orderBy('r.id', 'DESC');
        $circQb = $circuitAvisRepository->createQueryBuilder('c')->where('c.user = :user')->setParameter('user', $user)->orderBy('c.id', 'DESC');

        $hebPag = $paginator->paginate($hebQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);
        $actPag = $paginator->paginate($actQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);
        $circPag = $paginator->paginate($circQb->getQuery(), $request->query->getInt('page', 1), 5, ['sort' => '']);

        return $this->render('user/avis.html.twig', [
            'hebergements' => $hebPag,
            'activities' => $actPag,
            'circuits' => $circPag,
        ]);
    }

    #[Route('/mes-circuits', name: 'user_circuits')]
    public function circuits(
        Request $request,
        PaginatorInterface $paginator,
        CircuitRepository $circuitRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $qb = $circuitRepository->createQueryBuilder('c')
            ->where('c.createur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.id', 'DESC');

        $circuits = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 8, ['sort' => '']);

        return $this->render('user/circuits.html.twig', [
            'circuits' => $circuits,
        ]);
    }

    #[Route('/mes-reservations/hebergement/{id}/annuler', name: 'user_hebergement_cancel', methods: ['POST'])]
    public function cancelHebergementReservation(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($reservation->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $reservation->setStatut('ANNULEE');
        $em->flush();
        $this->addFlash('success', 'Réservation hébergement annulée.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/mes-reservations/activite/{id}/annuler', name: 'user_activity_cancel', methods: ['POST'])]
    public function cancelActivityReservation(Booking $booking, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($booking->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $booking->setStatus('CANCELLED');
        $em->flush();
        $this->addFlash('success', 'Réservation activité annulée.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/mes-reservations/circuit/{id}/annuler', name: 'user_circuit_cancel', methods: ['POST'])]
    public function cancelCircuitReservation(ReservationCircuit $reservation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($reservation->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $reservation->setStatut('ANNULEE');
        $em->flush();
        $this->addFlash('success', 'Réservation circuit annulée.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/mes-avis/hebergement/{id}/supprimer', name: 'user_hebergement_avis_delete', methods: ['POST'])]
    public function deleteHebergementAvis(Avis $avis, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($avis->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $em->remove($avis);
        $em->flush();
        $this->addFlash('success', 'Avis hébergement supprimé.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/mes-avis/activite/{id}/supprimer', name: 'user_activity_avis_delete', methods: ['POST'])]
    public function deleteActivityAvis(Review $review, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($review->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $em->remove($review);
        $em->flush();
        $this->addFlash('success', 'Avis activité supprimé.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/mes-avis/circuit/{id}/supprimer', name: 'user_circuit_avis_delete', methods: ['POST'])]
    public function deleteCircuitAvis(CircuitAvis $avis, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($avis->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $em->remove($avis);
        $em->flush();
        $this->addFlash('success', 'Avis circuit supprimé.');
        return $this->redirectToRoute('user_avis');
    }

    #[Route('/mes-circuits/{id}/supprimer', name: 'user_circuit_delete', methods: ['POST'])]
    public function deleteCircuit(Circuit $circuit, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $currentUserId = $this->getUser()?->getId();
        if ($circuit->getCreator()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }
        $em->remove($circuit);
        $em->flush();
        $this->addFlash('success', 'Circuit personnalisé supprimé.');
        return $this->redirectToRoute('user_circuits');
    }
>>>>>>> testsisi
}
