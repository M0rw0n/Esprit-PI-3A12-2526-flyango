<?php

namespace App\Service\Api;

class QrCodeService
{
    public function generateQrCode(
        string $data,
        string $format = 'png',
        int $size = 300,
        string $foregroundColor = '#000000',
        string $backgroundColor = '#FFFFFF',
        int $margin = 10
    ): string {
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
        
        if ($format === 'svg') {
            $apiUrl .= "&format=svg";
        }
        
        if ($format === 'png' || $format === 'webp') {
            return $apiUrl . "&format=png";
        }
        
        return $apiUrl;
    }

    public function generateBookingQrCode(string $bookingId, string $passengerName, string $flightInfo): string
    {
        $qrData = json_encode([
            'type' => 'booking',
            'id' => $bookingId,
            'passenger' => $passengerName,
            'flight' => $flightInfo,
            'generated' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

        return $this->generateQrCode($qrData);
    }

    public function generateTicketQrCode(string $ticketId, string $event, string $date): string
    {
        $qrData = json_encode([
            'type' => 'ticket',
            'id' => $ticketId,
            'event' => $event,
            'date' => $date,
            'generated' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

        return $this->generateQrCode($qrData);
    }

    public function generatePaymentQrCode(string $amount, string $currency, string $merchantId): string
    {
        $qrData = json_encode([
            'type' => 'payment',
            'amount' => $amount,
            'currency' => $currency,
            'merchant' => $merchantId,
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);

        return $this->generateQrCode($qrData);
    }

    public function generateVcardQrCode(string $name, string $email, string $phone, string $company = ''): string
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:$name\n";
        $vcard .= "EMAIL:$email\n";
        $vcard .= "TEL:$phone\n";
        if ($company) $vcard .= "ORG:$company\n";
        $vcard .= "END:VCARD";

        return $this->generateQrCode($vcard);
    }

    public function generateWifiQrCode(string $ssid, string $password, string $encryption = 'WPA'): string
    {
        $wifi = "WIFI:T:$encryption;S:$ssid;P:$password;;";
        return $this->generateQrCode($wifi);
    }

    public function generateUrlQrCode(string $url): string
    {
        return $this->generateQrCode($url);
    }
}