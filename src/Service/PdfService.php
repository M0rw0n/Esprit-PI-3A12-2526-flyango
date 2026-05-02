<?php

namespace App\Service;

use Knp\Snappy\Pdf;

class PdfService
{
    private Pdf $pdf;

    public function __construct(string $projectDir)
    {
        $this->pdf = new Pdf($projectDir . '/vendor/bin/wkhtmltopdf');
        $this->pdf->setOptions([
            'enable-local-file-access' => true,
            'no-outline' => true,
            'disable-javascript' => true,
            'orientation' => 'Portrait',
            'page-size' => 'A4',
            'margin-top' => 20,
            'margin-bottom' => 20,
            'margin-left' => 15,
            'margin-right' => 15,
            'footer-center' => 'Fly&Go - Page [page]/[toPage]',
            'footer-font-size' => 8,
        ]);
    }

    public function generateFromHtml(string $html, string $filename): string
    {
        return $this->pdf->getOutputFromHtml($html, [
            'filename' => $filename,
        ]);
    }

    public function generateFromUrl(string $url, string $filename): string
    {
        return $this->pdf->getOutput($url, [
            'filename' => $filename,
        ]);
    }

    public function generateReservationPdf(array $data): string
    {
        $html = $this->renderReservationTemplate($data);
        $ref = $data['reference'] ?? 'RES-' . date('Ymd');
        return $this->generateFromHtml($html, "reservation_$ref.pdf");
    }

    public function generateCircuitPdf(array $circuit): string
    {
        $html = $this->renderCircuitTemplate($circuit);
        $ref = $circuit['nom'] ?? 'CIRCUIT';
        return $this->generateFromHtml($html, "circuit_$ref.pdf");
    }

    private function renderReservationTemplate(array $data): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1B3A6B; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #1B3A6B; }
        .title { font-size: 24px; margin: 20px 0; color: #1B3A6B; }
        .info { margin: 15px 0; }
        .info-label { font-weight: bold; color: #666; display: inline-block; width: 150px; }
        .amount { font-size: 24px; color: #FFB700; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">✈️ Fly&Go</div>
        <div>Confirmation de réservation</div>
    </div>
    <div class="title">Réf: {$data['reference']}</div>
    <div class="info"><span class="info-label">Client:</span> {$data['client']}</div>
    <div class="info"><span class="info-label">Service:</span> {$data['type']}</div>
    <div class="info"><span class="info-label">Date:</span> {$data['date']}</div>
    <div class="info"><span class="info-label">Montant:</span> <span class="amount">{$data['amount']} TND</span></div>
    <div class="footer">
        <p>Merci pour votre confiance | Fly&Go - Votre agence de voyage en Tunisie</p>
    </div>
</body>
</html>
HTML;
    }

    private function renderCircuitTemplate(array $circuit): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1B3A6B; }
        .title { font-size: 28px; color: #1B3A6B; margin: 20px 0; }
        .price { font-size: 24px; color: #FFB700; font-weight: bold; }
        .section { margin: 20px 0; }
        .section h2 { color: #1B3A6B; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✈️ Fly&Go</h1>
    </div>
    <h1 class="title">{$circuit['nom']}</h1>
    <p class="price">{$circuit['prix']} TND - {$circuit['duree']} jours</p>
    <div class="section">
        <h2>Description</h2>
        <p>{$circuit['description']}</p>
    </div>
    <div class="section">
        <h2>Inclus</h2>
        <p>{$circuit['inclus']}</p>
    </div>
</body>
</html>
HTML;
    }
}
