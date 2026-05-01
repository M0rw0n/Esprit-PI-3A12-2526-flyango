<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Service\PdfInvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/reservation/{id}/facture', name: 'reservation_invoice', methods: ['GET'])]
    public function generateInvoice(Reservation $reservation, PdfInvoiceService $pdfService): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();
        if ($userId !== null && $reservation->getUser()?->getId() !== $userId && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $pdfService->generateAndDownload($reservation);
    }

    #[Route('/reservation/{id}/facture/preview', name: 'reservation_invoice_preview', methods: ['GET'])]
    public function previewInvoice(Reservation $reservation, PdfInvoiceService $pdfService): Response
    {
        $user = $this->getUser();
        $userId = $user?->getId();
        if ($userId !== null && $reservation->getUser()?->getId() !== $userId && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $pdfService->generateAndStream($reservation);
    }

    #[Route('/facture/{id}', name: 'public_facture', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function publicFacture(int $id, ReservationRepository $repo): Response
    {
        $reservation = $repo->find($id);
        if (!$reservation || $reservation->getStatut() !== 'CONFIRMEE') {
            throw $this->createNotFoundException('Facture non disponible');
        }

        return $this->render('pdf/reservation_invoice.html.twig', [
            'reservation' => $reservation,
            'hebergement' => $reservation->getHebergement(),
            'user' => $reservation->getUser(),
            'date' => new \DateTime(),
        ]);
    }
}
