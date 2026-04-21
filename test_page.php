<?php
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$html = @file_get_contents('http://localhost:8000/messenger/', false, $ctx);
echo 'Length: ' . strlen($html) . "\n";
echo 'Has showNewChatModal: ' . (strpos($html, 'showNewChatModal') !== false ? 'YES' : 'NO') . "\n";
echo 'Has switchMessengerTab: ' . (strpos($html, 'switchMessengerTab') !== false ? 'YES' : 'NO') . "\n";
echo 'Has messenger-container: ' . (strpos($html, 'messenger-container') !== false ? 'YES' : 'NO') . "\n";