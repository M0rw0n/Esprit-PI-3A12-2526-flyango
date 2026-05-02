<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TransportBooking;
use App\Entity\User;
use App\Repository\TransportOfferRepository;
use App\Repository\TransportBookingRepository;
use App\Service\Api\PaymentService;
use App\Service\Api\MailerService;
use App\Service\ReceiptService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transport')]
class TransportController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ReceiptService $receiptService,
        private readonly ?MailerService $mailerService = null,
    ) {}
    #[Route('', name: 'transport_index', methods: ['GET'])]
    public function index(Request $request, TransportOfferRepository $repo, PaginatorInterface $paginator): Response
    {
        $q = $request->query->get('q');
        $type = $request->query->get('type');
        $depart = $request->query->get('depart');
        $arrival = $request->query->get('arrival');
        $sort = $request->query->get('sort', 'date_asc');

        $qb = $repo->createQueryBuilder('t')->orderBy('t.departureDatetime', $sort === 'date_desc' ? 'DESC' : 'ASC');
        if ($q) {
            $qb->andWhere('t.departCity LIKE :q OR t.arrivalCity LIKE :q')->setParameter('q', '%' . $q . '%');
        }
        if ($type) {
            $qb->andWhere('t.transportType = :type')->setParameter('type', $type);
        }
        if ($depart) {
            $qb->andWhere('t.departCity = :depart')->setParameter('depart', $depart);
        }
        if ($arrival) {
            $qb->andWhere('t.arrivalCity = :arrival')->setParameter('arrival', $arrival);
        }

        $offers = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 9, ['sort' => '']);
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
            
            $persons = (int) $request->request->get('passengers', 1);
            $paymentMethod = $request->request->get('payment_method', 'stripe');
            $totalPrice = $offer->getPrice() * $persons;
            
            $bookingData = [
                'type' => 'TRANSPORT',
                'service_name' => $offer->getCompanyName() . ' - ' . $offer->getRoute(),
                'service_date' => $offer->getDepartureDatetime()->format('Y-m-d'),
                'total_amount' => $totalPrice,
                'unit_price' => $offer->getPrice(),
                'quantity' => $persons,
                'customer_name' => $user->getFullName(),
                'customer_email' => $user->getEmail(),
                'customer_phone' => $user->getTelephone() ?? '',
                'payment_method' => $paymentMethod,
                'transport_id' => $offer->getId(),
                'user_id' => $user->getId(),
            ];
            
            $result = $this->processPayment($bookingData, $paymentMethod, $user);
            
            if (!$result['success']) {
                $this->addFlash('error', 'Paiement échoué: ' . ($result['error'] ?? 'Erreur inconnue'));
                return $this->redirectToRoute('transport_show', ['id' => $id]);
            }
            
            $booking = new TransportBooking();
            $booking->setTransportOffer($offer)
                ->setUser($user)
                ->setPassengers($persons)
                ->setTravelClass($request->request->get('travel_class', 'ECONOMY'))
                ->setPickupLocation($request->request->get('pickup_location'))
                ->setDropoffLocation($request->request->get('dropoff_location'))
                ->setCustomerName($user->getFullName())
                ->setCustomerEmail($user->getEmail())
                ->setCustomerPhone($user->getTelephone())
                ->setPickupDatetime($offer->getDepartureDatetime())
                ->setDropoffDatetime($offer->getArrivalDatetime())
                ->setTotalPrice($totalPrice)
                ->setStatus('PAID')
                ->setBookingRef($result['booking_id'])
                ->setPaymentMethod($paymentMethod)
                ->setPaymentIntentId($result['transaction_id']);
            
            $em->persist($booking);
            $em->flush();

            if ($this->mailerService) {
                $this->mailerService->sendReservationConfirmation(
                    $user->getEmail(),
                    $user->getFullName(),
                    $offer->getCompanyName() . ' - ' . $offer->getRoute(),
                    $result['booking_id'],
                    $offer->getDepartureDatetime()->format('d/m/Y'),
                    $totalPrice,
                    'TRANSPORT'
                );
            }

            $this->addFlash('success', '✅ Transport reservé et payé avec succès!');
            return $this->redirectToRoute('payment_confirmation', ['bookingId' => $result['booking_id']]);
        }

        return $this->render('transport/show.html.twig', ['offer' => $offer]);
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
