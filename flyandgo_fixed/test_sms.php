<?php
$hash = 'ffa45c6e-62ed-455e-9d4d-a37a12a7720c';
$token = '9cfd779a-aa29-4530-b39d-a13a6499ea24';

// Try different sender IDs
$senders = ['SMS', 'FlyAndGo', 'flyandgo', 'TEST'];

foreach ($senders as $sender) {
    $phone = '21624306909';
    $url = "https://api.smspm.com?hash=$hash&toNumber=$phone&text=Test+$sender&fromNumber=$sender&token=$token";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    $status = $data['messages'][0]['status'] ?? 'error';
    echo "Sender '$sender': $status\n";
}