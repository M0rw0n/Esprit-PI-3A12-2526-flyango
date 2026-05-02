<?php

namespace App\Service;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use TCPDF;

class ReceiptService
{
    public function __construct(
        private readonly \App\Service\Api\QrCodeService $qrCodeService,
    ) {}
    public function generateReceiptPdf(array $data): string
    {
        $html = $this->generateReceiptHtml($data);
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Fly&Go');
        $pdf->SetAuthor('Fly&Go');
        $pdf->SetTitle('Reçu de ' . ($data['type'] ?? 'ACTIVITE'));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return $pdf->Output('recus_flyandgo_' . ($data['booking_id'] ?? 'unknown') . '.pdf', 'S');
    }

    private function formatPrice(float $price): string
    {
        return number_format($price, 2, ',', ' ') . ' €';
    }

    public function generateReceiptHtml(array $data): string
    {
        $type = $data['type'] ?? 'ACTIVITE';
        $priceFormatted = $this->formatPrice($data['total_amount'] ?? 0);
        $tvaFormatted = $this->formatPrice(($data['total_amount'] ?? 0) * 0.19);
        $totalFormatted = $this->formatPrice(($data['total_amount'] ?? 0) * 1.19);
        $unitFormatted = $this->formatPrice($data['unit_price'] ?? 0);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; color: #333; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: 900; color: #1e3a8a; }
        .logo span { color: #f59e0b; }
        .receipt-title { font-size: 24px; font-weight: 700; color: #1e3a8a; text-align: center; margin-bottom: 30px; }
        .receipt-number { background: #f59e0b; color: #fff; padding: 8px 16px; border-radius: 4px; font-weight: 700; font-size: 14px; }
        .info-section { display: flex; gap: 40px; margin-bottom: 30px; }
        .info-box { flex: 1; background: #f8fafc; padding: 20px; border-radius: 8px; }
        .info-box h3 { color: #1e3a8a; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748b; font-size: 13px; }
        .info-value { font-weight: 600; color: #1e3a8a; font-size: 14px; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th { background: #1e3a8a; color: #fff; padding: 14px; text-align: left; font-size: 12px; text-transform: uppercase; }
        .items-table td { padding: 14px; border-bottom: 1px solid #e2e8f0; }
        .total-section { background: #f0f9ff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 16px; }
        .total-row.final { font-size: 22px; font-weight: 900; color: #1e3a8a; border-top: 2px solid #1e3a8a; padding-top: 15px; margin-top: 10px; }
        .footer { margin-top: 40px; text-align: center; color: #64748b; font-size: 12px; }
        .footer p { margin: 5px 0; }
        .qr-section { display: flex; align-items: center; gap: 20px; margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; }
        .qr-section img { width: 100px; height: 100px; }
        .qr-info { font-size: 12px; color: #64748b; }
        .status-paid { background: #10b981; color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Fly<span>&</span>Go</div>
        <div class="receipt-number">N ' . $data['booking_id'] . '</div>
    </div>
    
    <div class="receipt-title">REÇU DE ' . $type . '</div>
    
    <div class="info-section">
        <div class="info-box">
            <h3>Informations Client</h3>
            <div class="info-row"><span class="info-label">Nom</span><span class="info-value">' . ($data['customer_name'] ?? 'N/A') . '</span></div>
            <div class="info-row"><span class="info-label">Email</span><span class="info-value">' . ($data['customer_email'] ?? 'N/A') . '</span></div>
            <div class="info-row"><span class="info-label">Téléphone</span><span class="info-value">' . ($data['customer_phone'] ?? 'N/A') . '</span></div>
        </div>
        <div class="info-box">
            <h3>Détails de la Réservation</h3>
            <div class="info-row"><span class="info-label">Type</span><span class="info-value">' . $type . '</span></div>
            <div class="info-row"><span class="info-label">Service</span><span class="info-value">' . ($data['service_name'] ?? 'N/A') . '</span></div>
            <div class="info-row"><span class="info-label">Date</span><span class="info-value">' . ($data['service_date'] ?? 'N/A') . '</span></div>
            <div class="info-row"><span class="info-label">Statut</span><span class="status-paid">PAYE</span></div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Prix Unitaire</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>' . ($data['service_name'] ?? 'N/A') . '</td>
                <td>' . $unitFormatted . '</td>
                <td>' . ($data['quantity'] ?? 1) . '</td>
                <td>' . $priceFormatted . '</td>
            </tr>
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-row"><span>Sous-total</span><span>' . $priceFormatted . '</span></div>
        <div class="total-row"><span>TVA (19%)</span><span>' . $tvaFormatted . '</span></div>
        <div class="total-row final"><span>Total Payé</span><span>' . $totalFormatted . '</span></div>
    </div>
    
    <div class="qr-section">
        <img src="' . ($data['qr_code'] ?? '') . '" alt="QR Code">
        <div class="qr-info">
            <p><strong>Code de confirmation:</strong> ' . ($data['booking_id'] ?? '') . '</p>
            <p>Scannez ce code pour accéder à votre réservation</p>
            <p>Date d\'émission: ' . ($data['created_at'] ?? date('d/m/Y H:i')) . '</p>
        </div>
    </div>
    
    <div class="footer">
        <p>Merci pour votre confiance avec Fly&Go!</p>
        <p>Fly&Go - Votre partenaire de voyage en Tunisie</p>
        <p>support@flyandgo.tn - +216 58 000 000</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    public function generateReceiptWord(array $data): string
    {
        if (!class_exists('ZipArchive')) {
            return $this->generateReceiptWordFallback($data);
        }
        
        if (!class_exists('PhpOffice\PhpWord\PhpWord')) {
            return $this->generateReceiptWordFallback($data);
        }
        
        try {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            
            $section->addText('Fly&Go - Reçu de ' . ($data['type'] ?? 'ACTIVITE'), ['size' => 24, 'bold' => true, 'color' => '1E3A8A']);
            $section->addText('N° de réservation: ' . ($data['booking_id'] ?? ''), ['size' => 14]);
            $section->addTextBreak();
            
            $section->addText('Informations Client', ['bold' => true, 'size' => 14, 'color' => '1E3A8A']);
            $section->addText('Nom: ' . ($data['customer_name'] ?? 'N/A'));
            $section->addText('Email: ' . ($data['customer_email'] ?? 'N/A'));
            $section->addText('Téléphone: ' . ($data['customer_phone'] ?? 'N/A'));
            $section->addTextBreak();
            
            $section->addText('Détails de la Réservation', ['bold' => true, 'size' => 14, 'color' => '1E3A8A']);
            $section->addText('Type: ' . ($data['type'] ?? 'ACTIVITE'));
            $section->addText('Service: ' . ($data['service_name'] ?? 'N/A'));
            $section->addText('Date: ' . ($data['service_date'] ?? 'N/A'));
            $section->addTextBreak();
            
            $price = number_format(($data['total_amount'] ?? 0) * 1.19, 2, ',', ' ');
            $section->addText('Prix: ' . $price . ' € (TVA incluse)');
            $section->addTextBreak();
            
            $section->addText('Merci pour votre confiance avec Fly&Go!', ['italic' => true]);
            
            $filename = 'receipt_' . ($data['booking_id'] ?? 'unknown') . '.docx';
            $tempFile = sys_get_temp_dir() . '/' . $filename;
            
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            return $tempFile;
        } catch (\Exception $e) {
            return $this->generateReceiptWordFallback($data);
        }
    }
    
    private function generateReceiptWordFallback(array $data): string
    {
        $content = "FLY&GO - REÇU DE " . ($data['type'] ?? 'ACTIVITE') . "\n";
        $content .= "========================================\n\n";
        $content .= "N° de réservation: " . ($data['booking_id'] ?? 'N/A') . "\n\n";
        $content .= "INFORMATIONS CLIENT\n";
        $content .= "-------------------\n";
        $content .= "Nom: " . ($data['customer_name'] ?? 'N/A') . "\n";
        $content .= "Email: " . ($data['customer_email'] ?? 'N/A') . "\n";
        $content .= "Téléphone: " . ($data['customer_phone'] ?? 'N/A') . "\n\n";
        $content .= "DÉTAILS DE LA RÉSERVATION\n";
        $content .= "--------------------------\n";
        $content .= "Type: " . ($data['type'] ?? 'ACTIVITE') . "\n";
        $content .= "Service: " . ($data['service_name'] ?? 'N/A') . "\n";
        $content .= "Date: " . ($data['service_date'] ?? 'N/A') . "\n";
        
        $price = number_format(($data['total_amount'] ?? 0) * 1.19, 2, ',', ' ');
        $content .= "\nPrix: " . $price . " € (TVA incluse)\n\n";
        $content .= "Merci pour votre confiance avec Fly&Go!\n";
        
        $filename = 'receipt_' . ($data['booking_id'] ?? 'unknown') . '.txt';
        $tempFile = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($tempFile, $content);
        
        return $tempFile;
    }

    public function generateQrCode(string $data): string
    {
        try {
            return $this->qrCodeService->generateQrCode($data, 'png', 200);
        } catch (\Exception $e) {
            $encoded = base64_encode($data);
            return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="#fff" width="100" height="100"/><text x="50" y="50" font-family="Arial" font-size="10" text-anchor="middle">QR: ' . substr($encoded, 0, 20) . '...</text><rect x="20" y="20" width="60" height="60" fill="none" stroke="#333" stroke-width="2"/></svg>');
        }
    }

    public function createReceipt(array $bookingData): array
    {
        $bookingId = 'FG-' . date('Ymd') . '-' . strtoupper(uniqid());
        
        $qrData = json_encode([
            'booking_id' => $bookingId,
            'service' => $bookingData['service_name'] ?? 'Unknown',
            'date' => $bookingData['service_date'] ?? date('Y-m-d')
        ]);
        
        $qrCode = $this->generateQrCode($qrData);
        
        $receiptData = array_merge($bookingData, [
            'booking_id' => $bookingId,
            'qr_code' => $qrCode,
            'created_at' => date('d/m/Y H:i'),
            'status' => 'PAID'
        ]);
        
        return [
            'success' => true,
            'receipt' => $receiptData,
            'pdf_data' => base64_encode($this->generateReceiptPdf($receiptData)),
            'word_path' => $this->generateReceiptWord($receiptData),
            'qr_code' => $qrCode,
            'booking_id' => $bookingId
        ];
    }
}