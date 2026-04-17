<?php

namespace App\Service;

use Exception;

class SmsService
{
    private string $apiKey;
    private string $senderId;
    private string $provider;
    
    public function __construct()
    {
        $apiKey = 'ffa45c6e-62ed-455e-9d4d-a37a12a7720c';
        $this->apiKey = $_SERVER['SMS_API_KEY'] ?? $apiKey;
        $this->senderId = 'FlyAndGo';
        $this->provider = 'smspm';
    }
    
    public function sendReservationConfirmation(string $phone, array $reservation): bool
    {
        $ref = 'FG-' . $reservation['id'];
        $heb = $reservation['hebergement'];
        $checkin = $reservation['checkin'];
        $checkout = $reservation['checkout'];
        $total = $reservation['total'];
        
        $message = "Fly&Go: Reservation $ref confirmee!\n\n";
        $message .= "Hebergement: $heb\n";
        $message .= "Arrivee: $checkin\n";
        $message .= "Depart: $checkout\n";
        $message .= "Montant: $total TND\n\n";
        $message .= "Merci de votre confiance! Scannez le QR code pour votre facture.";
        
        return $this->send($phone, $message);
    }
    
    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            error_log("SMS API Key not configured");
            return false;
        }
        
        $phone = $this->formatPhone($phone);
        if (!$phone) {
            error_log("Invalid phone number: " . $phone);
            return false;
        }
        
        try {
            return match($this->provider) {
                'twilio' => $this->sendTwilio($phone, $message),
                'africastalking' => $this->sendAfricaTalking($phone, $message),
                'messagebird' => $this->sendMessageBird($phone, $message),
                'smsroute' => $this->sendSmsRoute($phone, $message),
                'messagemedia' => $this->sendMessageMedia($phone, $message),
                'springedge' => $this->sendSpringEdge($phone, $message),
                'smspm' => $this->sendSmsPm($phone, $message),
                default => $this->sendFreeSms($phone, $message),
            };
        } catch (\Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendTwilio(string $phone, string $message): bool
    {
        $accountSid = $_SERVER['TWILIO_ACCOUNT_SID'] ?? '';
        $authToken = $_SERVER['TWILIO_AUTH_TOKEN'] ?? '';
        
        if (empty($accountSid) || empty($authToken)) {
            return $this->sendFallbackSms($phone, $message);
        }
        
        $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json";
        
        $data = [
            'To' => $phone,
            'From' => $this->senderId,
            'Body' => $message
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function sendAfricaTalking(string $phone, string $message): bool
    {
        $username = $_SERVER['AT_USERNAME'] ?? '';
        
        $data = [
            'username' => $username,
            'to' => $phone,
            'message' => $message
        ];
        
        $ch = curl_init('https://api.africastalking.com/restless/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'ApiKey: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function sendMessageBird(string $phone, string $message): bool
    {
        $data = [
            'to' => $phone,
            'from' => $this->senderId,
            'body' => $message
        ];
        
        $ch = curl_init('https://rest.messagebird.com/messages');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: AccessKey ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function sendSmsRoute(string $phone, string $message): bool
    {
        $data = [
            'api_key' => $this->apiKey,
            'to' => $phone,
            'message' => $message,
            'sender_id' => $this->senderId
        ];
        
        $ch = curl_init('https://smsroute.pro/api/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function sendMessageMedia(string $phone, string $message): bool
    {
        $data = [
            'message' => $message,
            'recipients' => [$phone],
            'sender_id' => $this->senderId
        ];
        
        $ch = curl_init('https://api.messagemedia.com/v1/messages');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->apiKey . ':')
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function sendSmsPm(string $phone, string $message): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 8 && in_array($phone[0], ['2', '5', '9'])) {
            $phone = '216' . $phone;
        } elseif (strlen($phone) === 9 && str_starts_with($phone, '0')) {
            $phone = '216' . substr($phone, 1);
        }
        
        $hash = 'ffa45c6e-62ed-455e-9d4d-a37a12a7720c';
        $token = '9cfd779a-aa29-4530-b39d-a13a6499ea24';
        
        $url = "https://api.smspm.com?hash=$hash&toNumber=$phone&text=" . urlencode($message) . "&fromNumber=sms&token=$token";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("SMS sent to $phone: HTTP $httpCode - $response");
        return $httpCode === 200;
    }
    
    private function sendFreeSms(string $phone, string $message): bool
    {
        $apiKey = $this->apiKey;
        
        if (empty($apiKey) || $apiKey === 'your_sms_api_key_here') {
            error_log("SMS API not configured - skipping SMS to $phone");
            return false;
        }
        
        $data = [
            'key' => $apiKey,
            'phone' => $phone,
            'message' => $message,
            'sender' => $this->senderId
        ];
        
        $endpoints = [
            'https://www.bulksms.com/_t/n/w/c',
            'https://sms.postex.com/api/send',
            'https://www.txtlocal.com/api/'
        ];
        
        foreach ($endpoints as $endpoint) {
            try {
                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode >= 200 && $httpCode < 300) {
                    error_log("Free SMS sent via $endpoint");
                    return true;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        error_log("All free SMS providers failed");
        return false;
    }
    
    private function sendFallbackSms(string $phone, string $message): bool
    {
        $apiKey = $this->apiKey;
        
        if (empty($apiKey) || $apiKey === 'your_sms_api_key_here') {
            error_log("SMS not configured - message not sent to $phone");
            return false;
        }
        
        $data = [
            'api_key' => $apiKey,
            'to' => $phone,
            'message' => $message,
            'from' => $this->senderId
        ];
        
        $ch = curl_init('https://sms.flyandgo.tn/api/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("SMS response: $httpCode - $response");
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    private function formatPhone(string $phone): string|false
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '216')) {
            return '+' . $phone;
        }
        
        if (str_starts_with($phone, '0')) {
            return '+216' . substr($phone, 1);
        }
        
        if (strlen($phone) === 8 && in_array($phone[0], ['2', '5', '9'])) {
            return '+216' . $phone;
        }
        
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        
        return false;
    }
}