<?php
// Simple SSE Server for Fly&Go Notifications
// Run with: php -S localhost:3000 sse.php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/../var//notifications.json';
$lastId = isset($_SERVER['HTTP_LAST_EVENT_ID']) ? (int)$_SERVER['HTTP_LAST_EVENT_ID'] : 0;

while (true) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && $data['id'] > $lastId) {
            echo "event: " . $data['event'] . "\n";
            echo "data: " . json_encode($data['data']) . "\n\n";
            $lastId = $data['id'];
        }
    }
    
    if (connection_aborted()) break;
    sleep(1);
}