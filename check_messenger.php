<?php
$context = stream_context_create([
    'http' => [
        'header' => "Cookie: " . ($_COOKIE['PHPSESSID'] ?? '')
    ]
]);
$html = file_get_contents('http://localhost:8000/messenger/', false, $context);
file_put_contents('messenger_output.html', $html);
echo "Saved to messenger_output.html\n";
echo "Contains showNewChatModal: " . (strpos($html, 'showNewChatModal') !== false ? 'YES' : 'NO') . "\n";
echo "Contains switchMessengerTab: " . (strpos($html, 'switchMessengerTab') !== false ? 'YES' : 'NO') . "\n";