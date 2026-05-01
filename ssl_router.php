<?php

$projectDir = __DIR__;
$certsDir = $projectDir . '/config/certs';
$cert = $certsDir . '/server.crt';
$key = $certsDir . '/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    die("Certificats manquants. Lancez: php generate_cert.php\n");
}

$port = 8443;

$cmd = sprintf(
    'php -S %s %d -t public "%s"',
    'localhost',
    $port,
    $cert
);

echo "========================================\n";
echo "   Fly&Go - HTTPS Server\n";
echo "========================================\n";
echo "URL: https://localhost:$port\n";
echo "Ctrl+C pour arreter\n";
echo "========================================\n";

proc_close(proc_open($cmd, ['pipe', $pipe]));
sleep(1);
echo "Serveur demarre!\n";
fgets(STDIN);