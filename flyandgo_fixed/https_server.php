<?php

$projectDir = __DIR__;
$certsDir = $projectDir . '/config/certs';
$cert = $certsDir . '/server.crt';
$key = $certsDir . '/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    die("Certificats manquants. Generez-les: php generate_cert.php\n");
}

$port = 8443;
$host = 'localhost';

$context = stream_context_create([
    'ssl' => [
        'local_cert' => $cert,
        'local_pk' => $key,
        'allow_self_signed' => true,
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$server = stream_socket_server(
    "ssl://$host:$port",
    $errno,
    $errstr,
    STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
    $context
);

if (!$server) {
    die("Erreur: $errstr ($errno)\n");
}

echo "========================================\n";
echo "   Fly&Go - Serveur HTTPS\n";
echo "========================================\n";
echo "URL: https://localhost:$port\n";
echo "Acceptez le certificat auto-signe!\n";
echo "Ctrl+C pour arreter\n";
echo "========================================\n";

while ($client = @stream_socket_accept($server, 5)) {
    $request = fread($client, 8192);
    
    if (preg_match('/^(GET|POST|PUT|DELETE|OPTIONS|PATCH) \/([^\s]*)/', $request, $m)) {
        $path = '/' . $m[2];
        if (strpos($path, '?') !== false) {
            $path = parse_url($path, PHP_URL_PATH);
        }
        if ($path === '/' || $path === '') {
            $file = $projectDir . '/public/index.php';
        } else {
            $file = $projectDir . '/public' . $path;
        }
        
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        
        if (file_exists($file) && is_file($file) && $ext === 'php') {
            chdir(dirname($file));
            ob_start();
            include $file;
            $body = ob_get_clean();
            $contentType = 'text/html';
        } elseif (file_exists($file) && is_file($file)) {
            $body = file_get_contents($file);
            $contentType = getContentType($ext);
        } else {
            $body = '404 Not Found';
            $contentType = 'text/plain';
            http_response_code(404);
        }
    } else {
        $body = '400 Bad Request';
        $contentType = 'text/plain';
        http_response_code(400);
    }
    
    $response = "HTTP/1.1 200 OK\r\nContent-Type: $contentType\r\nContent-Length: " . strlen($body) . "\r\nConnection: close\r\n\r\n" . $body;
    fwrite($client, $response);
    fclose($client);
}

function getContentType($ext) {
    $types = [
        'html' => 'text/html',
        'htm' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    return $types[$ext] ?? 'application/octet-stream';
}