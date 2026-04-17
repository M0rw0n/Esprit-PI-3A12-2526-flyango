<?php
$ctx = stream_context_create([
    'http' => [
        'timeout' => 10,
        'follow_location' => 1
    ]
]);

$url = 'http://localhost:8000/messenger/';
$headers = get_headers($url, 0, $ctx);
echo "Headers:\n";
print_r($headers);

$html = @file_get_contents($url, false, $ctx);
echo "\n\nContent length: " . strlen($html) . "\n";
echo "First 500 chars:\n" . substr($html, 0, 500) . "\n";