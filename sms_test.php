<?php
// Simple text message
$hash = 'ffa45c6e-62ed-455e-9d4d-a37a12a7720c';
$token = '9cfd779a-aa29-4530-b39d-a13a6499ea24';
$phone = '21624306909';
$msg = 'Fly&Go Test Direct - Verifiez si vous recevez ce message SMS!';

$ch = curl_init("https://api.smspm.com?hash=$hash&toNumber=$phone&text=" . urlencode($msg) . "&fromNumber=SMS&token=$token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);

echo "SMS envoye! Verify ton telephone: $phone\n";
echo "Reponse: $res\n";