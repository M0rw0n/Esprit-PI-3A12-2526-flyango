<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/sse/hebergement', name: 'sse_hebergement')]
    public function hebergementStream(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $lastId = (int)($_GET['lastId'] ?? 0);
            $file = sys_get_temp_dir() . '/flyandgo_notifications.json';
            
            while (true) {
                if (file_exists($file)) {
                    $all = json_decode(file_get_contents($file), true) ?: [];
                    foreach ($all as $notif) {
                        if ($notif['id'] > $lastId) {
                            echo "event: " . $notif['event'] . "\n";
                            echo "data: " . json_encode($notif['data']) . "\n\n";
                            $lastId = $notif['id'];
                        }
                    }
                }
                
                if (connection_aborted()) break;
                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    #[Route('/sse/test', name: 'sse_test')]
    public function testNotification(): \Symfony\Component\HttpFoundation\Response
    {
        $service = new \App\Service\NotificationService();
        
        $type = $_GET['type'] ?? 'test';
        $message = $_GET['message'] ?? 'Test notification';
        
        $service->notifyHebergementCreated(new \App\Entity\Hebergement());
        
        return $this->json(['success' => true, 'message' => 'Notification sent: ' . $message]);
    }
}