<?php
$ch = curl_init();
$url = "https://api-inference.huggingface.co/models/intfloat/multilingual-e5-small";
$token = "YOUR_TOKEN_HERE"; // I will replace this in the next step or use ENV

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . getenv('HUGGINGFACE_API_KEY'),
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["inputs" => "Hello world"]));

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Code: " . $info['http_code'] . "\n";
if ($error) echo "Curl Error: $error\n";
echo "Response: $response\n";
