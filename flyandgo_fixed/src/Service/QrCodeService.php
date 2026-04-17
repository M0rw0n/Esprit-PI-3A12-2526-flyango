<?php

namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Label\LabelAlignment;

class QrCodeService
{
    public function generateBookingQrCode(string $bookingReference, int $amount): string
    {
        $data = json_encode([
            'ref' => $bookingReference,
            'amount' => $amount,
            'currency' => 'TND',
            'merchant' => 'Fly&Go',
            'timestamp' => time()
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data(base64_encode($data))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeMode())
            ->build();

        return $result->getDataUri();
    }

    public function generateTicketQrCode(string $ticketCode, array $ticketData): string
    {
        $data = json_encode([
            'code' => $ticketCode,
            'event' => $ticketData['event'] ?? 'Fly&Go Ticket',
            'date' => $ticketData['date'] ?? date('Y-m-d'),
            'client' => $ticketData['client'] ?? '',
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data(base64_encode($data))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(200)
            ->margin(10)
            ->build();

        return $result->getDataUri();
    }

    public function generateVCardQrCode(string $name, string $phone, string $email, string $website): string
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\nFN:$name\nTEL:$phone\nEMAIL:$email\nURL:$website\nEND:VCARD";

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($vcard)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(200)
            ->margin(10)
            ->build();

        return $result->getDataUri();
    }

    public function generateUrlQrCode(string $url): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(250)
            ->margin(10)
            ->build();

        return $result->getDataUri();
    }
}
