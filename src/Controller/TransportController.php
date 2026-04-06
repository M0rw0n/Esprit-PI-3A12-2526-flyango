<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TransportBooking;
use App\Entity\User;
use App\Repository\TransportOfferRepository;
use App\Repository\TransportBookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transport')]
class TransportController extends AbstractController
{
    #[Route('', name: 'transport_index', methods: ['GET'])]
    public function index(Request $request, TransportOfferRepository $repo): Response
    {
        $q = $request->query->get('q');
        $type = $request->query->get('type');
        $depart = $request->query->get('depart');
        $arrival = $request->query->get('arrival');
        $sort = $request->query->get('sort', 'date_asc');

        $offers = $repo->search($type, $depart, $arrival, null, $sort, $q);
        $cities = $repo->getDistinctCities();

        return $this->render('transport/index.html.twig', [
            'offers' => $offers,
            'cities' => $cities,
            'q' => $q,
            'type' => $type,
            'depart' => $depart,
            'arrival' => $arrival,
            'sort' => $sort,
        ]);
    }

    #[Route('/offre/{id}', name: 'transport_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, TransportOfferRepository $repo): Response
    {
        $offer = $repo->find($id);
        if (!$offer) {
            throw $this->createNotFoundException();
        }

        return $this->render('transport/show.html.twig', ['offer' => $offer]);
    }

    #[Route('/offre/{id}/reserver', name: 'transport_book', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function book(int $id, Request $request, TransportOfferRepository $offerRepo, EntityManagerInterface $em): Response
    {
        $offer = $offerRepo->find($id);
        if (!$offer) {
            throw $this->createNotFoundException();
        }

        if ($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            /** @var User $user */
            $user = $this->getUser();

            $booking = new TransportBooking();
            $booking->setTransportOffer($offer)
                ->setUser($user)
                ->setPassengers((int) $request->request->get('passengers', 1))
                ->setTravelClass($request->request->get('travel_class'))
                ->setPickupLocation($request->request->get('pickup_location'))
                ->setDropoffLocation($request->request->get('dropoff_location'))
                ->setCustomerName($user->getFullName())
                ->setCustomerEmail($user->getEmail())
                ->setCustomerPhone($user->getTelephone())
                ->setPickupDatetime($offer->getDepartureDatetime())
                ->setDropoffDatetime($offer->getArrivalDatetime())
                ->setTotalPrice($offer->getPrice() * (int) $request->request->get('passengers', 1))
                ->setStatus('PENDING');

            $em->persist($booking);
            $em->flush();

            $this->addFlash('success', '🎉 Réservation transport ajoutée à votre espace.');
            return $this->redirectToRoute('transport_my_bookings');
        }

        return $this->render('transport/show.html.twig', ['offer' => $offer]);
    }

    #[Route('/mes-reservations', name: 'transport_my_bookings', methods: ['GET'])]
    public function myBookings(TransportBookingRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('transport/my_bookings.html.twig', ['bookings' => $repo->findByUser($user)]);
    }

    #[Route('/reservation/{id}/annuler', name: 'transport_cancel_booking', methods: ['POST'])]
    public function cancelBooking(int $id, TransportBookingRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $booking = $repo->find($id);
        $currentUserId = $this->getUser()?->getId();

        if (!$booking || $booking->getUser()?->getId() !== $currentUserId) {
            throw $this->createAccessDeniedException();
        }

        $booking->setStatus('CANCELLED');
        $em->flush();

        $this->addFlash('success', 'Réservation annulée.');
        return $this->redirectToRoute('transport_my_bookings');
    }
}
