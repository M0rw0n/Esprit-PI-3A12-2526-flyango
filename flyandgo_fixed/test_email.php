<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'from' => 'onboarding@resend.dev',
    'to' => ['flyandgo.contact@gmail.com'],
    'subject' => 'Test - Reservation FG-999',
    'html' => '<h1>✅ Test email</h1><p>Reference: FG-999</p><p>Hotel: Test Hotel</p><p>Check-in: 01/06/2026</p><p>Check-out: 05/06/2026</p><p>Total: 500 TND</p><img src="https://via.placeholder.com/150" width="150">'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer re_gEG5W52G_HCpF1KxGJAvfHtQj7m3oN8d7',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP: $code\nResponse: $r\n";