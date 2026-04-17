<?php
require __DIR__.'/vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$request = \Symfony\Component\HttpFoundation\Request::create('/messenger/', 'GET');
$request->setSession($container->get('session'));

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
echo "Has messenger: " . (strpos($content, 'messenger') !== false ? 'YES' : 'NO') . "\n";
echo "Has showNewChatModal: " . (strpos($content, 'showNewChatModal') !== false ? 'YES' : 'NO') . "\n";
echo "Has switchMessengerTab: " . (strpos($content, 'switchMessengerTab') !== false ? 'YES' : 'NO') . "\n";