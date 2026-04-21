<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\TransportBooking;
use App\Service\Api\PaymentService;
use App\Service\ReceiptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ReceiptService $receiptService,
        private readonly EntityManagerInterface $em,
    ) {}

    private function getBookingData(string $bookingId): ?array
    {
        $booking = $this->em->getRepository(Booking::class)->findOneBy(['bookingReference' => $bookingId]);
        
        if (!$booking) {
            $booking = $this->em->getRepository(Booking::class)->find($bookingId);
        }
        
        if ($booking) {
            $activity = $booking->getActivity();
            return [
                'booking_id' => $booking->getBookingReference() ?? $booking->getId(),
                'type' => 'ACTIVITÉ',
                'service_name' => $activity ? $activity->getTitle() : 'Activité',
                'service_date' => $booking->getBookingDate() ? $booking->getBookingDate()->format('d/m/Y') : date('d/m/Y'),
                'total_amount' => $booking->getTotalPrice(),
                'unit_price' => $activity ? $activity->getPrice() : $booking->getTotalPrice(),
                'quantity' => $booking->getPersons(),
                'customer_name' => $booking->getCustomerName(),
                'customer_email' => $booking->getEmail(),
                'customer_phone' => $booking->getClientPhone() ?? '',
                'created_at' => $booking->getCreatedAt() ? $booking->getCreatedAt()->format('d/m/Y H:i') : date('d/m/Y H:i'),
                'status' => $booking->getStatus() ?? 'PAID'
            ];
        }
        
        $transportBooking = $this->em->getRepository(TransportBooking::class)->findOneBy(['bookingRef' => $bookingId]);
        
        if ($transportBooking) {
            $offer = $transportBooking->getTransportOffer();
            return [
                'booking_id' => $transportBooking->getBookingRef() ?? $transportBooking->getId(),
                'type' => 'TRANSPORT',
                'service_name' => $offer ? ($offer->getCompanyName() . ' - ' . $offer->getRoute()) : 'Transport',
                'service_date' => $transportBooking->getPickupDatetime() ? $transportBooking->getPickupDatetime()->format('d/m/Y') : date('d/m/Y'),
                'total_amount' => $transportBooking->getTotalPrice(),
                'unit_price' => $offer ? $offer->getPrice() : $transportBooking->getTotalPrice(),
                'quantity' => $transportBooking->getPassengers(),
                'customer_name' => $transportBooking->getCustomerName(),
                'customer_email' => $transportBooking->getCustomerEmail(),
                'customer_phone' => $transportBooking->getCustomerPhone() ?? '',
                'created_at' => $transportBooking->getCreatedAt() ? $transportBooking->getCreatedAt()->format('d/m/Y H:i') : date('d/m/Y H:i'),
                'status' => $transportBooking->getStatus() ?? 'PAID'
            ];
        }
        
        return null;
    }

    #[Route('/payment/create-intent', name: 'payment_create_intent', methods: ['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $amount = (float) ($data['amount'] ?? 0);
        $type = $data['type'] ?? 'activity';
        
        if ($amount <= 0) {
            return $this->json(['success' => false, 'error' => 'Montant invalide'], 400);
        }

        $result = $this->paymentService->createStripePaymentIntent($amount, 'eur', 'Fly&Go ' . $type);
        
        return $this->json($result);
    }

    #[Route('/payment/create-order', name: 'payment_create_order', methods: ['POST'])]
    public function createPaypalOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $amount = (float) ($data['amount'] ?? 0);
        $description = $data['description'] ?? 'Fly&Go Payment';
        
        if ($amount <= 0) {
            return $this->json(['success' => false, 'error' => 'Montant invalide'], 400);
        }

        $result = $this->paymentService->createPaypalOrder($amount, 'EUR', $description);
        
        return $this->json($result);
    }

    #[Route('/payment/process', name: 'payment_process', methods: ['POST'])]
    public function processPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $type = $data['type'] ?? 'activity';
        $serviceName = $data['service_name'] ?? '';
        $serviceDate = $data['service_date'] ?? date('Y-m-d');
        $amount = (float) ($data['amount'] ?? 0);
        
        $customerName = $data['customer_name'] ?? '';
        $customerEmail = $data['customer_email'] ?? '';
        $customerPhone = $data['customer_phone'] ?? '';
        
        $paymentMethod = $data['payment_method'] ?? 'stripe';
        
        if ($paymentMethod === 'paypal') {
            $orderResult = $this->paymentService->createPaypalOrder($amount, 'EUR', $serviceName);
            if (!$orderResult['success']) {
                return $this->json(['success' => false, 'error' => 'Erreur PayPal'], 500);
            }
            
            $captureResult = $this->paymentService->capturePaypalOrder($orderResult['order_id'] ?? '');
            
            $bookingData = [
                'type' => strtoupper($type),
                'service_name' => $serviceName,
                'service_date' => $serviceDate,
                'total_amount' => $amount,
                'unit_price' => $amount,
                'quantity' => 1,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'payment_method' => 'paypal',
                'order_id' => $orderResult['order_id'] ?? ''
            ];
        } else {
            $stripeResult = $this->paymentService->createStripePaymentIntent($amount, 'eur', $serviceName);
            
            $bookingData = [
                'type' => strtoupper($type),
                'service_name' => $serviceName,
                'service_date' => $serviceDate,
                'total_amount' => $amount,
                'unit_price' => $amount,
                'quantity' => 1,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'payment_method' => 'stripe',
                'payment_intent_id' => $stripeResult['payment_intent_id'] ?? ''
            ];
        }
        
        $receipt = $this->receiptService->createReceipt($bookingData);
        
        return $this->json([
            'success' => true,
            'payment' => [
                'method' => $paymentMethod,
                'status' => 'completed',
                'transaction_id' => $bookingData['order_id'] ?? $bookingData['payment_intent_id'] ?? ''
            ],
            'receipt' => $receipt['receipt'],
            'booking_id' => $receipt['booking_id']
        ]);
    }

    #[Route('/payment/receipt/{bookingId}', name: 'payment_receipt', methods: ['GET'])]
    public function getReceipt(string $bookingId): JsonResponse
    {
        return $this->json([
            'success' => true,
            'booking_id' => $bookingId,
            'message' => 'Receipt data would be retrieved from database'
        ]);
    }

    #[Route('/payment/download/pdf/{bookingId}', name: 'payment_download_pdf', methods: ['GET'])]
    public function downloadPdf(string $bookingId): Response
    {
        $bookingData = $this->getBookingData($bookingId);
        
        if (!$bookingData) {
            $bookingData = [
                'booking_id' => $bookingId,
                'type' => 'ACTIVITÉ',
                'service_name' => 'Activité',
                'service_date' => date('d/m/Y'),
                'total_amount' => 0,
                'unit_price' => 0,
                'quantity' => 1,
                'customer_name' => 'Client',
                'customer_email' => 'client@email.com',
                'customer_phone' => '',
                'created_at' => date('d/m/Y H:i'),
                'status' => 'PAID'
            ];
        }
        
        $pdfContent = $this->receiptService->generateReceiptPdf($bookingData);
        
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'recus_flyandgo_' . $bookingId . '.pdf'
            )
        );
        
        return $response;
    }

    #[Route('/payment/download/word/{bookingId}', name: 'payment_download_word', methods: ['GET'])]
    public function downloadWord(string $bookingId): Response
    {
        $bookingData = $this->getBookingData($bookingId);
        
        if (!$bookingData) {
            $bookingData = [
                'booking_id' => $bookingId,
                'type' => 'ACTIVITÉ',
                'service_name' => 'Activité',
                'service_date' => date('d/m/Y'),
                'total_amount' => 0,
                'customer_name' => 'Client',
                'customer_email' => 'client@email.com',
                'customer_phone' => ''
            ];
        }
        
        $wordPath = $this->receiptService->generateReceiptWord($bookingData);
        
        $fileContent = file_get_contents($wordPath);
        $extension = pathinfo($wordPath, PATHINFO_EXTENSION);
        $filename = 'recus_flyandgo_' . $bookingId . '.docx';
        
        if ($extension === 'txt') {
            $filename = 'recus_flyandgo_' . $bookingId . '.txt';
            return new Response($fileContent, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }
        
        return new Response($fileContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($fileContent)
        ]);
    }

    #[Route('/payment/qrcode/{bookingId}', name: 'payment_qrcode', methods: ['GET'])]
    public function getQrCode(string $bookingId): Response
    {
        $qrData = json_encode([
            'booking_id' => $bookingId,
            'date' => date('Y-m-d'),
            'service' => 'Fly&Go'
        ]);
        
        $qrCodeUrl = $this->receiptService->generateQrCode($qrData);
        
        return new Response(
            '<html><body><img src="' . $qrCodeUrl . '" alt="QR Code"/></body></html>',
            200,
            ['Content-Type' => 'text/html']
        );
    }

    #[Route('/payment/confirmation/{bookingId}', name: 'payment_confirmation', methods: ['GET'])]
    public function confirmation(string $bookingId): Response
    {
        $bookingData = $this->getBookingData($bookingId);
        
        return $this->render('payment/confirmation.html.twig', [
            'booking_id' => $bookingId,
            'booking_data' => $bookingData
        ]);
    }
}