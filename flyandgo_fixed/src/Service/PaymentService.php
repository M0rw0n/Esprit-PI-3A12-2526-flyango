<?php

namespace App\Service;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentService
{
    private string $resendApiKey;
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $router;
    private RequestStack $requestStack;
    private string $projectDir;

    public function __construct(
        string $resendApiKey,
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        RequestStack $requestStack,
        string $projectDir
    ) {
        $this->resendApiKey = $resendApiKey;
        $this->em = $em;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->projectDir = $projectDir;
    }

    public function createStripeCheckoutSession(Reservation $reservation): string
    {
        $host = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost() ?: 'https://flyandgo.tn';
        
        $reservation->setPaymentId('demo_' . uniqid());
        $reservation->setPaymentMethod('DEMO');
        $this->em->flush();
        
        return $host . '/payment/success?session_id=demo&reservation=' . $reservation->getId();
    }

    public function verifyStripePayment(string $sessionId, int $reservationId): bool
    {
        return $sessionId === 'demo' || !empty($sessionId);
    }

    public function processConfirmedPayment(Reservation $reservation): void
    {
        error_log("=== processConfirmedPayment START ===");
        
        $qrCodePath = $this->generateQRCode($reservation);
        error_log("QR Code: " . $qrCodePath);
        
        $facturePath = $this->generateFacturePdf($reservation);
        error_log("Facture: " . $facturePath);

        $reservation->setQrCode($qrCodePath);
        $reservation->setFacturePdf($facturePath);
        $reservation->setStatut(Reservation::STATUT_CONFIRMEE);
        $reservation->setPaidAt(new \DateTime());

        $this->em->flush();
        
        $this->sendConfirmationSms($reservation);

        // Send email with full reservation info
        $this->sendConfirmationEmail($reservation);
        error_log("=== processConfirmedPayment END ===");
    }

    public function generateQRCode(Reservation $reservation): string
    {
        try {
            $reservationId = $reservation->getId();
            $clientName = $reservation->getNomClient();
            $hebName = $reservation->getHebergement()->getNom();
            $checkin = $reservation->getDateDebut()->format('d/m/Y');
            $checkout = $reservation->getDateFin()->format('d/m/Y');
            $total = number_format($reservation->getMontantTotal(), 3, ',', ' ') . ' TND';
            $ref = 'FG-' . $reservationId;
            
            $qrData = "RESERVATION #$ref\nClient: $clientName\nHebergement: $hebName\nArrivee: $checkin\nDepart: $checkout\nMontant: $total\n\nScannez pour voir la facture sur flyandgo.tn/facture/$reservationId";
            
            $qrCodeService = new \App\Service\Api\QrCodeService();
            return $qrCodeService->generateQrCode($qrData, 'png', 300);
        } catch (\Exception $e) {
            error_log("QR Code Error: " . $e->getMessage());
            return '';
        }
    }

    public function getFactureUrl(Reservation $reservation): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'flyandgo.tn';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $protocol . '://' . $host . '/facture/' . $reservation->getId();
    }

public function generateFacturePdf(Reservation $reservation): string
    {
        $h = $reservation->getHebergement();
        $ref = 'FG-' . $reservation->getId();
        $date = $reservation->getCreatedAt()->format('d/m/Y');
        $nom = $reservation->getNomClient();
        $email = $reservation->getEmailClient();
        $telephone = $reservation->getTelephone() ?: 'N/A';
        $checkin = $reservation->getDateDebut()->format('d/m/Y');
        $checkout = $reservation->getDateFin()->format('d/m/Y');
        $total = $reservation->getMontantTotal();
        $nbrPersonnes = $reservation->getNombrePersonnes();
        $hebNom = $h->getNom();
        $hebVille = $h->getVille() ?: '';
        $hebAdresse = $h->getAdresse() ?: '';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 40px; color: #333; max-width: 800px; margin: 0 auto; background: white; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1b436b; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #1b436b; }
        .logo span { color: #00b4d8; }
        .facture-title { font-size: 24px; color: #1b436b; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 25px; gap: 20px; }
        .info-box { background: #f8f9fa; padding: 20px; border-radius: 8px; flex: 1; }
        .info-box h3 { color: #1b436b; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; }
        .info-box p { font-size: 14px; line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        th { background: #1b436b; color: white; padding: 14px; text-align: left; font-size: 13px; }
        td { padding: 14px; border-bottom: 1px solid #eee; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        .total-row { background: #1b436b; color: white; }
        .total-row td { font-size: 18px; font-weight: bold; padding: 18px 14px; }
        .total-amount { font-size: 22px; }
        .footer { margin-top: 40px; text-align: center; color: #666; font-size: 13px; border-top: 1px solid #eee; padding-top: 25px; }
        .status { display: inline-block; background: #10b981; color: white; padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        @media print { body { padding: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">✈️ Fly<span>&Go</span></div>
        <div class="facture-title">FACTURE</div>
    </div>
    
    <div class="info-section">
        <div class="info-box">
            <h3>Informations client</h3>
            <p><strong>Nom:</strong> $nom<br>
            <strong>Email:</strong> $email<br>
            <strong>Téléphone:</strong> $telephone</p>
        </div>
        <div class="info-box">
            <h3>Détails réservation</h3>
            <p><strong>Référence:</strong> $ref<br>
            <strong>Date:</strong> $date<br>
            <strong>Statut:</strong> <span class="status">PAYÉE</span></p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Détails</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Hébergement</strong></td>
                <td>$hebNom</td>
            </tr>
            <tr>
                <td><strong>Adresse</strong></td>
                <td>$hebAdresse, $hebVille</td>
            </tr>
            <tr>
                <td><strong>Date d'arrivée</strong></td>
                <td>$checkin</td>
            </tr>
            <tr>
                <td><strong>Date de départ</strong></td>
                <td>$checkout</td>
            </tr>
            <tr>
                <td><strong>Nombre de personnes</strong></td>
                <td>$nbrPersonnes</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL À PAYER</strong></td>
                <td class="total-amount">$total TND</td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Fly&Go</strong> - Votre plateforme de réservation de confiance</p>
        <p>contact@flyandgo.tn | www.flyandgo.tn</p>
        <p style="margin-top: 12px; color: #999;">Merci pour votre confiance!</p>
    </div>
</body>
</html>
HTML;

        $dir = $this->projectDir . '/public/uploads/factures';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = 'facture_' . $reservation->getId() . '.html';
        file_put_contents($dir . '/' . $filename, $html);
        return 'uploads/factures/' . $filename;
    }

    private function sendConfirmationEmail(Reservation $reservation): void
    {
        $to = $reservation->getEmailClient();
        $nom = $reservation->getNomClient();
        $ref = 'FG-' . $reservation->getId();
        $heb = $reservation->getHebergement()->getNom();
        $adresse = $reservation->getHebergement()->getAdresse() ?: '';
        $ville = $reservation->getHebergement()->getVille() ?: '';
        $checkin = $reservation->getDateDebut()->format('d/m/Y');
        $checkout = $reservation->getDateFin()->format('d/m/Y');
        $total = $reservation->getMontantTotal();
        $nbrPersonnes = $reservation->getNombrePersonnes();
        $telephone = $reservation->getTelephone() ?: '';

        $baseUrl = 'http://127.0.0.1:8000';
        
        $qrImg = '';
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:Arial,sans-serif">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f5;padding:20px">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:white;border-radius:16px;overflow:hidden">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1b436b,#0a1f33);padding:40px 30px;text-align:center">
                            <h1 style="margin:0;color:white;font-size:28px;font-weight:bold">✅ Réservation confirmée!</h1>
                            <p style="margin:10px 0 0;color:rgba(255,255,255,0.85)">Merci pour votre confiance avec Fly&Go</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px">
                            <p style="font-size:16px;color:#333;margin:0 0 5px">Bonjour <strong style="font-size:18px">' . htmlspecialchars($nom) . '</strong>,</p>
                            <p style="font-size:15px;color:#666;margin:0 0 25px">Votre réservation a été confirmée avec succès. Voici les détails:</p>
                            
                            <!-- Reservation Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:12px;margin:20px 0;border:1px solid #e9ecef">
                                <tr>
                                    <td style="padding:20px">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td colspan="2" style="border-bottom:1px solid #dee2e6;padding-bottom:12px;margin-bottom:12px">
                                                    <strong style="color:#1b436b;font-size:18px">📋 RÉSERVATION N° ' . $ref . '</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666;width:45%">🏨 <strong>Hébergement</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . htmlspecialchars($heb) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666">📍 <strong>Adresse</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . htmlspecialchars($adresse . ' ' . $ville) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666">📅 <strong>Check-in</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . $checkin . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666">📅 <strong>Check-out</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . $checkout . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666">👥 <strong>Personnes</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . $nbrPersonnes . ' personne(s)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;color:#666">📞 <strong>Téléphone</strong></td>
                                                <td style="padding:8px 0;font-weight:500">' . htmlspecialchars($telephone) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:15px 0 8px;border-top:2px solid #dee2e6;color:#666;font-size:16px">💰 <strong>Montant total</strong></td>
                                                <td style="padding:15px 0 8px;border-top:2px solid #dee2e6;font-weight:bold;font-size:24px;color:#1b436b">' . number_format($total, 3, ',', ' ') . ' TND</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- QR Code -->
                            <div style="background:#fff;border:2px dashed #1b436b;border-radius:12px;padding:25px;text-align:center;margin:25px 0">
                                <p style="margin:0 0 15px;font-size:16px;color:#1b436b;font-weight:bold">📱 QR Code de confirmation</p>
                                ' . $qrImg . '
                                <p style="margin:15px 0 0;font-size:13px;color:#888">Présentez ce code à l\'arrivée pour un check-in rapide</p>
                            </div>
                            
                            <!-- Footer -->
                            <div style="background:#f8f9fa;padding:20px;text-align:center;border-radius:12px;margin-top:20px">
                                <p style="margin:0;font-weight:bold;color:#1b436b;font-size:18px">Fly&Go</p>
                                <p style="margin:8px 0 0;color:#666;font-size:14px">📧 contact@flyandgo.tn | 🌐 www.flyandgo.tn</p>
                                <p style="margin:8px 0 0;color:#888;font-size:12px">Votre plateforme de réservation de confiance</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        // Try Resend API
        try {
            $emailSent = $this->sendViaResend($to, $nom, 'Réservation Fly&Go - ' . $ref . ' confirmée', $html);
            if ($emailSent) {
                error_log("SUCCESS: Email sent for reservation $ref");
            }
        } catch (\Exception $e) {
            error_log("ERROR sending email: " . $e->getMessage());
        }
    }

    private function sendViaResend(string $to, string $toName, string $subject, string $html): bool
    {
        error_log("=== sendViaResend called ===");
        error_log("To: $to, Name: $toName, Subject: $subject");
        
        $toEmail = 'flyandgo.contact@gmail.com';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'from' => 'onboarding@resend.dev',
            'to' => [$toEmail],
            'subject' => $subject,
            'html' => $html
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->resendApiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        error_log("HTTP Code: $httpCode, Response: $response, Error: $error");
        
        if ($httpCode === 200 || $httpCode === 201) {
            error_log("Email sent successfully: $subject");
            return true;
        } else {
            error_log("Email failed: HTTP $httpCode - $response - $error");
            return false;
        }
    }

    public function cancelReservation(Reservation $reservation): void
    {
        if ($reservation->getStatut() === Reservation::STATUT_EN_ATTENTE) {
            $reservation->setStatut(Reservation::STATUT_ANNULEE);
            $this->em->flush();
        }
    }
    
    private function sendConfirmationSms(Reservation $reservation): void
    {
        try {
            $telephone = $reservation->getTelephone();
            error_log("SMS Debug - Reservation ID: " . $reservation->getId() . ", Phone: " . $telephone);
            
            if (empty($telephone)) {
                error_log("No phone number for reservation " . $reservation->getId());
                return;
            }
            
            $ref = 'FG-' . $reservation->getId();
            $heb = $reservation->getHebergement()->getNom();
            $checkin = $reservation->getDateDebut()->format('d/m/Y');
            $checkout = $reservation->getDateFin()->format('d/m/Y');
            $total = number_format($reservation->getMontantTotal(), 3, ',', ' ') . ' TND';
            
            $message = "Fly&Go: Reservation $ref confirmee!\n\nHebergement: $heb\nArrivee: $checkin\nDepart: $checkout\nMontant: $total\n\nMerci de votre confiance!";
            
            $smsService = new SmsService();
            $result = $smsService->send($telephone, $message);
            
            error_log("SMS sent to $telephone: " . ($result ? 'success' : 'failed'));
        } catch (\Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
        }
    }
}