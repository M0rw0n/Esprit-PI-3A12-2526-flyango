<?php

namespace App\Service;

use App\Entity\Circuit;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CircuitPdfService
{
    public function __construct(
        private Environment $twig
    ) {}

    public function generateCircuitItineraryPdf(Circuit $circuit): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);

        $html = $this->twig->render('pdf/circuit_itinerary.html.twig', [
            'circuit' => $circuit,
            'date' => new \DateTime(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateAndDownload(Circuit $circuit): Response
    {
        $pdfContent = $this->generateCircuitItineraryPdf($circuit);
        
        $slug = $circuit->getSlug() ?: 'circuit-' . $circuit->getId();
        $filename = 'itinerary_' . $slug . '.pdf';

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    public function generateAndStream(Circuit $circuit): Response
    {
        $pdfContent = $this->generateCircuitItineraryPdf($circuit);
        
        $slug = $circuit->getSlug() ?: 'circuit-' . $circuit->getId();
        $filename = 'itinerary_' . $slug . '.pdf';

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}