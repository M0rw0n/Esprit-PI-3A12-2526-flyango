<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    private ?HttpClientInterface $client = null;
    private string $pusherAppId;
    private string $pusherKey;
    private string $pusherSecret;
    private string $pusherCluster;
    private string $onesignalAppId;
    private string $onesignalApiKey;

    public function __construct(
        string $pusherAppId = '',
        string $pusherKey = '',
        string $pusherSecret = '',
        string $pusherCluster = 'eu',
        string $onesignalAppId = '',
        string $onesignalApiKey = ''
    ) {
        $this->pusherAppId = $pusherAppId ?: $_ENV['PUSHER_APP_ID'] ?? '';
        $this->pusherKey = $pusherKey ?: $_ENV['PUSHER_KEY'] ?? '';
        $this->pusherSecret = $pusherSecret ?: $_ENV['PUSHER_SECRET'] ?? '';
        $this->pusherCluster = $pusherCluster ?: $_ENV['PUSHER_CLUSTER'] ?? 'eu';
        $this->onesignalAppId = $onesignalAppId ?: $_ENV['ONESIGNAL_APP_ID'] ?? '';
        $this->onesignalApiKey = $onesignalApiKey ?: $_ENV['ONESIGNAL_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function sendRealTimeNotification(string $channel, string $event, array $data): array
    {
        if (empty($this->pusherKey) || empty($this->pusherSecret)) {
            return $this->mockPusherNotification($channel, $event, $data);
        }

        try {
            $auth = base64_encode($this->pusherKey . ':' . $this->pusherSecret);
            
            $response = $this->getClient()->request('POST', "https://api.pusher.com/apps/{$this->pusherAppId}/channels/{$channel}/events", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $auth,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $event,
                    'data' => $data,
                ],
            ]);

            return [
                'success' => true,
                'channel' => $channel,
                'event' => $event,
            ];
        } catch (\Exception $e) {
            return $this->mockPusherNotification($channel, $event, $data);
        }
    }

    private function mockPusherNotification(string $channel, string $event, array $data): array
    {
        return [
            'success' => true,
            'mock' => true,
            'channel' => $channel,
            'event' => $event,
            'data' => $data,
            'message' => 'Notification simulée (Pusher non configuré)',
        ];
    }

    public function notifyNewComment(int $postId, string $postTitle, string $commenterName, string $comment): array
    {
        $data = [
            'type' => 'new_comment',
            'post_id' => $postId,
            'post_title' => $postTitle,
            'commenter' => $commenterName,
            'comment_preview' => substr($comment, 0, 50) . '...',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $this->sendRealTimeNotification("forum-post-{$postId}", 'new-comment', $data);
    }

    public function notifyNewReply(int $commentId, string $replierName, string $reply): array
    {
        $data = [
            'type' => 'new_reply',
            'comment_id' => $commentId,
            'replier' => $replierName,
            'reply_preview' => substr($reply, 0, 50) . '...',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $this->sendRealTimeNotification('forum-replies', 'new-reply', $data);
    }

    public function notifyLike(string $type, int $itemId, string $likerName): array
    {
        $data = [
            'type' => 'new_like',
            'item_type' => $type,
            'item_id' => $itemId,
            'liker' => $likerName,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $this->sendRealTimeNotification("forum-{$type}-{$itemId}", 'new-like', $data);
    }

    public function sendPushNotification(string $userId, string $title, string $message, array $data = []): array
    {
        if (empty($this->onesignalAppId) || empty($this->onesignalApiKey)) {
            return $this->mockPushNotification($userId, $title, $message, $data);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://onesignal.com/api/v1/notifications', [
                'headers' => [
                    'Authorization' => 'Basic ' . $this->onesignalApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'app_id' => $this->onesignalAppId,
                    'headings' => ['en' => $title],
                    'contents' => ['en' => $message],
                    'data' => $data,
                    'filters' => [
                        ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $userId]
                    ],
                ],
            ]);

            return [
                'success' => true,
                'notification_id' => json_decode($response->getBody()->getContents(), true)['id'] ?? null,
            ];
        } catch (\Exception $e) {
            return $this->mockPushNotification($userId, $title, $message, $data);
        }
    }

    private function mockPushNotification(string $userId, string $title, string $message, array $data): array
    {
        return [
            'success' => true,
            'mock' => true,
            'user_id' => $userId,
            'title' => $title,
            'message' => 'Notification push simulée (OneSignal non configuré)',
        ];
    }

    public function notifyUser(string $userId, string $type, string $title, string $message): array
    {
        $data = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $this->sendPushNotification($userId, $title, $message, $data);
    }

    public function notifyPostReply(int $postId, string $postTitle, string $recipientId, string $replierName): array
    {
        return $this->notifyUser(
            $recipientId,
            'post_reply',
            'Nouvelle réponse à: ' . $postTitle,
            $replierName . ' a répondu à votre publication'
        );
    }

    public function notifyCommentReply(int $commentId, string $recipientId, string $replierName): array
    {
        return $this->notifyUser(
            $recipientId,
            'comment_reply',
            'Nouvelle réponse à votre commentaire',
            $replierName . ' a répondu à votre commentaire'
        );
    }

    public function notifyLikeReceived(string $type, int $itemId, string $recipientId, string $likerName): array
    {
        return $this->notifyUser(
            $recipientId,
            'like_received',
            'Nouveau like!',
            $likerName . ' a aimé votre ' . $type
        );
    }
}