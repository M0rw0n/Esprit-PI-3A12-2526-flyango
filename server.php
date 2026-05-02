<?php

$projectDir = __DIR__;
$certsDir = $projectDir . '/config/certs';

$cert = $certsDir . '/server.crt';
$key = $certsDir . '/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    echo "Certificats manquants. Lancez: php generate_cert.php\n";
    exit(1);
}

$port = 8443;
$host = 'localhost';

echo "========================================\n";
echo "   Fly&Go - Serveur HTTPS\n";
echo "========================================\n";
echo "URL: https://localhost:$port\n";
echo "Accceptez le certificat dans le navigateur!\n";
echo "Ctrl+C pour arreter\n";
echo "========================================\n\n";

$context = stream_context_create([
    'ssl' => [
        'local_cert' => $cert,
        'local_pk' => $key,
        'allow_self_signed' => true,
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

if (!stream_socket_server("ssl://$host:$port", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context)) {
    die("Erreur: $errstr ($errno)\n");
}

echo "Serveur demarre!\n";

while ($con = @stream_socket_accept($con, 5)) {
    $request = fread($con, 8192);
    
    if (preg_match('/^GET \/([^?\s]*)/', $request, $m)) {
        $file = $m[1] ?: 'index.php';
        if ($file === 'index.php' || $file === '/') {
            $file = 'public/index.php';
        } else {
            $file = 'public/' . $file;
        }
        
        if (file_exists($file) && !is_dir($file)) {
            ob_start();
            include $file;
            $body = ob_get_clean();
            $response = "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nContent-Length: " . strlen($body) . "\r\n\r\n" . $body;
        } else {
            $response = "HTTP/1.1 404 Not Found\r\nContent-Length: 9\r\n\r\nNot Found";
        }
    } else {
        $response = "HTTP/1.1 400 Bad Request\r\nContent-Length: 0\r\n\r\n";
    }
    
    fwrite($con, $response);
    fclose($con);
}