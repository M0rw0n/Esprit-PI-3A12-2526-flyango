<?php
require __DIR__.'/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/var/cache/twig',
    'debug' => true,
    'auto_reload' => true,
]);

try {
    $html = $twig->render('messenger/index.html.twig', []);
    echo "Rendered length: " . strlen($html) . "\n";
    echo "Has showNewChatModal: " . (strpos($html, 'showNewChatModal') !== false ? 'YES' : 'NO') . "\n";
    echo "Has switchMessengerTab: " . (strpos($html, 'switchMessengerTab') !== false ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}