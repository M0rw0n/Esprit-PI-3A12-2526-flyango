<?php

$projectDir = __DIR__;
$certsDir = $projectDir . '/config/certs';
$cert = $certsDir . '/server.crt';
$key = $certsDir . '/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    die("Generez les certificats: php generate_cert.php\n");
}

$port = 8443;

$cmd = sprintf(
    'php -S localhost:%d -t public "%s" "%s"',
    $port,
    $cert,
    $key
);

echo "========================================\n";
echo "   Fly&Go - Serveur HTTPS\n";
echo "========================================\n";
echo "URL: https://localhost:$port\n";
echo "Ctrl+C pour arreter\n";
echo "========================================\n\n";

passthru($cmd);