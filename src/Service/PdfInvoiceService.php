<?php

namespace App\Service;

use App\Entity\Reservation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfInvoiceService extends AbstractController
{
    public function generateReservationPdf(Reservation $reservation): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);

        $html = $this->renderView('pdf/reservation_invoice.html.twig', [
            'reservation' => $reservation,
            'hebergement' => $reservation->getHebergement(),
            'user' => $reservation->getUser(),
            'date' => new \DateTime(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateAndDownload(Reservation $reservation): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generateReservationPdf($reservation);

        $filename = 'facture_' . $reservation->getId() . '_' . date('Ymd') . '.pdf';

        return new \Symfony\Component\HttpFoundation\Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]
        );
    }

    public function generateAndStream(Reservation $reservation): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generateReservationPdf($reservation);

        $filename = 'facture_' . $reservation->getId() . '_' . date('Ymd') . '.pdf';

        return new \Symfony\Component\HttpFoundation\Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]
        );
    }
}
