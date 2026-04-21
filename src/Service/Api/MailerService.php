<?php

namespace App\Service\Api;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerService
{
    public function __construct(
        private readonly ?MailerInterface $mailer = null,
    ) {}

    public function sendReservationConfirmation(
        string $toEmail,
        string $customerName,
        string $serviceName,
        string $bookingRef,
        string $date,
        float $amount,
        string $type = 'ACTIVITÉ'
    ): bool {
        if (!$this->mailer) {
            return false;
        }

        $typeLabel = match ($type) {
            'CIRCUIT' => 'Circuit',
            'TRANSPORT' => 'Transport',
            default => 'Activité',
        };

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1B3A6B, #00B4D8); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .booking-ref { background: #00B4D8; color: white; padding: 10px 20px; border-radius: 5px; font-size: 18px; font-weight: bold; }
        .details { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .details td { padding: 10px; border-bottom: 1px solid #eee; }
        .footer { background: #1B3A6B; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .btn { display: inline-block; background: #00B4D8; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0">✅ Réservation confirmée</h1>
            <p style="margin:10px 0 0">Fly&Go</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{$customerName}</strong>,</p>
            <p>Votre réservation de {$typeLabel} a été confirmée avec succès!</p>
            
            <div style="text-align:center;margin: 25px 0">
                <p style="margin-bottom:10px">Référence de réservation:</p>
                <div class="booking-ref">{$bookingRef}</div>
            </div>
            
            <table class="details" style="width:100%;border-collapse:collapse">
                <tr>
                    <td><strong>Service:</strong></td>
                    <td>{$serviceName}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>{$date}</td>
                </tr>
                <tr>
                    <td><strong>Montant:</strong></td>
                    <td style="font-weight:bold;color:#00B4D8">{$amount} TND</td>
                </tr>
                <tr>
                    <td><strong>Statut:</strong></td>
                    <td style="color:green">Confirmé</td>
                </tr>
            </table>
            
            <p style="margin-top:20px">
                Vous pouvez télécharger votre reçu depuis votre espace client.
            </p>
        </div>
        <div class="footer">
            <p style="margin:0">Merci de voyager avec <strong>Fly&Go</strong> ✈️</p>
            <p style="margin:10px 0 0;font-size:12px">Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
HTML;

        try {
            $email = (new Email())
                ->from(new Address('noreply@flyandgo.tn', 'Fly&Go'))
                ->to($toEmail)
                ->subject("Confirmation de réservation - {$bookingRef}")
                ->html($html);

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendBookingConfirmation(
        string $toEmail,
        string $customerName,
        array $bookingData
    ): bool {
        return $this->sendReservationConfirmation(
            $toEmail,
            $customerName,
            $bookingData['service_name'] ?? 'Service',
            $bookingData['booking_id'] ?? 'N/A',
            $bookingData['service_date'] ?? date('d/m/Y'),
            (float) ($bookingData['total_amount'] ?? 0),
            $bookingData['type'] ?? 'ACTIVITÉ'
        );
    }
}