<?php

$projectDir = __DIR__;
$cert = $projectDir . '/config/certs/server.crt';
$key = $projectDir . '/config/certs/server.key';

if (!file_exists($cert) || !file_exists($key)) {
    http_response_code(500);
    echo "Certificats manquants. Lancez: php generate_cert.php";
    exit;
}

$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);

if (is_dir($projectDir . '/public' . $path)) {
    $path = rtrim($path, '/') . '/index.php';
}

$file = $projectDir . '/public' . $path;

if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    chdir(dirname($file));
    require basename($file);
    exit;
}

if (file_exists($file) && !is_dir($file)) {
    readfile($file);
    exit;
}

http_response_code(404);
echo "404 Not Found";

exit;