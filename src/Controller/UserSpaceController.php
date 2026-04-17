<?php

namespace App\Controller;

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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSpaceController extends AbstractController
{
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
        ReservationRepository $reservationRepository,
        BookingRepository $bookingRepository,
        ReservationCircuitRepository $reservationCircuitRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/reservations.html.twig', [
            'hebergementReservations' => $reservationRepository->findBy(['user' => $user], ['createdAt' => 'DESC']),
            'activityReservations' => $bookingRepository->findBy(['user' => $user], ['id' => 'DESC']),
            'circuitReservations' => $reservationCircuitRepository->findBy(['user' => $user], ['dateReservation' => 'DESC']),
        ]);
    }

    #[Route('/mes-avis', name: 'user_avis')]
    public function avis(AvisRepository $avisRepository, ReviewRepository $reviewRepository, CircuitAvisRepository $circuitAvisRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/avis.html.twig', [
            'hebergementAvis' => $avisRepository->findBy(['user' => $user], ['id' => 'DESC']),
            'activityAvis' => $reviewRepository->findBy(['user' => $user], ['id' => 'DESC']),
            'circuitAvis' => $circuitAvisRepository->findBy(['user' => $user], ['id' => 'DESC']),
        ]);
    }

    #[Route('/mes-circuits', name: 'user_circuits')]
    public function circuits(CircuitRepository $circuitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/circuits.html.twig', [
            'circuits' => $circuitRepository->findUserCustomCircuits($user),
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
}
