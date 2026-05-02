<?php
// Simple SSE Server for real-time messaging
// This is a fallback when Mercure isn't available

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

$userId = $_GET['topic'] ?? '';
$userId = str_replace('call/', '', $userId);

// Send initial connection message
echo "data: " . json_encode(['type' => 'connected', 'userId' => $userId]) . "\n\n";
flush();

// Keep connection alive with heartbeat
$lastCheck = time();
$maxTime = 3600; // 1 hour max

while (connection_status() === CONNECTION_NORMAL && (time() - $lastCheck) < $maxTime) {
    // Check for new messages every 2 seconds
    $file = __DIR__ . '/var/mercure/' . $userId . '.json';
    
    if (file_exists($file)) {
        $data = file_get_contents($file);
        if ($data) {
            echo "data: " . $data . "\n\n";
            flush();
            @unlink($file);
        }
    }
    
    // Heartbeat every 10 seconds
    if (time() - $lastCheck > 10) {
        echo ": heartbeat\n\n";
        flush();
        $lastCheck = time();
    }
    
    sleep(2);
}

function publish($topic, $data) {
    $dir = __DIR__ . '/var/mercure';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $topic = str_replace('call/', '', $topic);
    $topic = str_replace('message/', '', $topic);
    file_put_contents($dir . '/' . $topic . '.json', json_encode($data));
}

// Handle POST requests (publishing)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'] ?? '';
    $data = $_POST['data'] ?? '';
    
    if ($topic && $data) {
        publish($topic, json_decode($data, true));
        echo 'OK';
    }
    exit;
}