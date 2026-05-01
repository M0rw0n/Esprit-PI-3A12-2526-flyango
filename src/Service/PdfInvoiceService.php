<?php

namespace App\Service;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfInvoiceService extends AbstractController
{
    public function generateReservationPdf(Reservation $reservation): string
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
    </div>
</body>
</html>
HTML;

        return $html;
    }

    public function generateAndDownload(Reservation $reservation): \Symfony\Component\HttpFoundation\Response
    {
        $html = $this->generateReservationPdf($reservation);
        
        $filename = 'facture_' . $reservation->getId() . '.html';
        
        return new \Symfony\Component\HttpFoundation\Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function generateAndStream(Reservation $reservation): \Symfony\Component\HttpFoundation\Response
    {
        $html = $this->generateReservationPdf($reservation);

        return new \Symfony\Component\HttpFoundation\Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html',
            ]
        );
    }
}