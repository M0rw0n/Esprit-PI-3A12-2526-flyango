<?php

$projectDir = __DIR__;
$certsDir = $projectDir . '/config/certs';
$cert = $certsDir . '/server.crt';
$key = $certsDir . '/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    echo "Generez les certificats d'abord: php generate_cert.php\n";
    exit(1);
}

$port = 8443;

echo "========================================\n";
echo "   Fly&Go - Serveur HTTPS\n";
echo "========================================\n";
echo "URL: https://localhost:$port\n";
echo "Accceptez le certificat auto-signe dans le navigateur!\n";
echo "Ctrl+C pour arreter\n";
echo "========================================\n\n";

$cmd = "php -S " . $port . " -t public " . $cert . " " . $key;
$descriptors = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);

$process = proc_open($cmd, $descriptors, $pipes);

if (is_resource($process)) {
    echo stream_get_contents($pipes[1]);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
}