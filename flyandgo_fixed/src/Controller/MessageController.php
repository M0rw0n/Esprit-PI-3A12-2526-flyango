<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\Conversation;
use App\Entity\ConversationParticipant;
use App\Entity\Message;
use App\Entity\MessageReaction;
use App\Entity\User;
use App\Repository\CallRepository;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[Route('/api/messages')]
class MessageController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
        private CallRepository $callRepository,
        private ?HubInterface $hub = null,
    ) {}

    private function getCurrentUser(): ?User
    {
        try {
            $token = $this->tokenStorage->getToken();
            $user = $token?->getUser();
            return $user instanceof User ? $user : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    #[Route('/conversations', name: 'api_messages_conversations', methods: ['GET'])]
    public function getConversations(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversations = $this->messageService->getUserConversations($user);
            $result = [];
            foreach ($conversations as $c) {
                if ($c instanceof Conversation) {
                    $result[] = $this->messageService->getConversationForApi($c, $user);
                }
            }

            return new JsonResponse([
                'conversations' => $result,
                'totalUnread' => $this->messageService->getTotalUnread($user),
                'userId' => $user->getId(),
            ]);
        } catch (\Throwable $e) {
            error_log('getConversations error: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    #[Route('/conversation/{id}', name: 'api_messages_conversation', methods: ['GET'])]
    public function getConversation(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $limit = (int) $request->query->get('limit', 50);
            $offset = (int) $request->query->get('offset', 0);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $this->messageService->markAsRead($conversation, $user);

            $messages = $this->messageService->getMessagesForApi($conversation, $user, $limit, $offset);

            return new JsonResponse([
                'messages' => $messages,
                'hasMore' => count($messages) === $limit,
                'conversation' => $this->messageService->getConversationForApi($conversation, $user)
            ]);
        } catch (\Throwable $e) {
            error_log('getConversation error: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/messages', name: 'api_messages_send', methods: ['POST'])]
    public function sendMessage(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Conversation not found'], 404);

            $data = json_decode($request->getContent(), true) ?: $request->request->all();
            $content = $data['content'] ?? '';

            if (!$content && !$request->files->get('image') && !$request->files->get('audio')) {
                return new JsonResponse(['error' => 'Empty message'], 400);
            }

            $message = $this->messageService->sendMessage($conversation, $user, $content);
            
            return new JsonResponse(['success' => true, 'message' => $this->formatMessage($message)]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/typing', name: 'api_messages_typing', methods: ['POST'])]
    public function typing(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if ($user) {
                $this->publishToTopic('conversation/' . $id, [
                    'type' => 'typing', 
                    'userId' => $user->getId(), 
                    'userName' => $user->getPrenom() . ' ' . $user->getNom(), 
                    'conversationId' => $id
                ]);
            }
            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse(['success' => false]);
        }
    }

    #[Route('/user/online', name: 'api_user_online', methods: ['POST'])]
    public function updateOnline(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if ($user) {
                $user->setLastSeenAt(new \DateTime());
                $this->em->flush();
                $this->publishToTopic('user/status', ['type' => 'user_online', 'userId' => $user->getId()]);
            }
            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            error_log('updateOnline error: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/upload-audio', name: 'api_messages_upload_audio', methods: ['POST'])]
    public function uploadAudio(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            $convId = $request->request->get('conversation_id');
            $file = $request->files->get('audio');
            
            if (!$user || !$convId || !$file) return new JsonResponse(['error' => 'Invalid request'], 400);

            $conversation = $this->em->getRepository(Conversation::class)->find($convId);
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/audio';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = uniqid() . '.webm';
            $file->move($uploadDir, $filename);
            $path = '/uploads/audio/' . $filename;

            $message = $this->messageService->sendMessage($conversation, $user, '🎤 Message vocal', null, $path);
            return new JsonResponse(['success' => true, 'message' => $this->formatMessage($message)]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/upload-image', name: 'api_messages_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            $convId = $request->request->get('conversation_id');
            $file = $request->files->get('image');
            
            if (!$user || !$convId || !$file) return new JsonResponse(['error' => 'Invalid request'], 400);

            $conversation = $this->em->getRepository(Conversation::class)->find($convId);
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/messages';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filename = uniqid() . '.' . ($file->guessExtension() ?: 'jpg');
            $file->move($uploadDir, $filename);
            $path = '/uploads/messages/' . $filename;

            $message = $this->messageService->sendMessage($conversation, $user, '', $path);
            return new JsonResponse(['success' => true, 'message' => $this->formatMessage($message)]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/read', name: 'api_messages_read', methods: ['POST'])]
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $this->messageService->markAsRead($conversation, $user);

            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function publishToTopic(string $topic, array $data): void
    {
        if ($this->hub) {
            try {
                $this->hub->publish(new Update($topic, json_encode($data), true));
            } catch (\Throwable $e) {
                error_log('Mercure publish error: ' . $e->getMessage());
            }
        }
    }

    private function formatMessage(Message $m): array
    {
        return [
            'id' => $m->getId(),
            'content' => $m->getContent(),
            'image' => $m->getImage(),
            'audio' => $m->getAudio(),
            'status' => $m->getStatus(),
            'createdAt' => $m->getCreatedAt()->format('c'),
            'sender' => $m->getSender() ? [
                'id' => $m->getSender()->getId(),
                'name' => $m->getSender()->getPrenom() . ' ' . $m->getSender()->getNom(),
                'avatar' => $m->getSender()->getAvatar()
            ] : null,
            'reactions' => $this->formatReactions($m),
            'replyTo' => $m->getReplyTo() ? [
                'id' => $m->getReplyTo()->getId(),
                'content' => substr($m->getReplyTo()->getContent(), 0, 100),
                'senderName' => $m->getReplyTo()->getSender() ? $m->getReplyTo()->getSender()->getPrenom() : 'Utilisateur'
            ] : null
        ];
    }

    private function formatReactions(Message $m): array
    {
        $reactions = [];
        $grouped = [];
        foreach ($m->getReactions() as $r) {
            $emoji = $r->getEmoji();
            if (!isset($grouped[$emoji])) {
                $grouped[$emoji] = ['emoji' => $emoji, 'count' => 0, 'users' => []];
            }
            $grouped[$emoji]['count']++;
            $grouped[$emoji]['users'][] = $r->getUser() ? $r->getUser()->getId() : 0;
        }
        return array_values($grouped);
    }

    #[Route('/message/{id}/react', name: 'api_messages_react', methods: ['POST'])]
    public function reactToMessage(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $message = $this->em->getRepository(Message::class)->find($id);
            if (!$message) return new JsonResponse(['error' => 'Message not found'], 404);

            $data = json_decode($request->getContent(), true);
            $emoji = $data['emoji'] ?? '👍';

            $existing = $this->em->getRepository(MessageReaction::class)->findOneBy([
                'message' => $message,
                'user' => $user
            ]);

            if ($existing) {
                if ($existing->getEmoji() === $emoji) {
                    $this->em->remove($existing);
                    $this->em->flush();
                    return new JsonResponse(['success' => true, 'removed' => true, 'emoji' => $emoji]);
                } else {
                    $existing->setEmoji($emoji);
                    $this->em->flush();
                    return new JsonResponse(['success' => true, 'updated' => true, 'emoji' => $emoji]);
                }
            } else {
                $reaction = new MessageReaction();
                $reaction->setMessage($message);
                $reaction->setUser($user);
                $reaction->setEmoji($emoji);
                $this->em->persist($reaction);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'added' => true, 'emoji' => $emoji]);
            }
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/theme', name: 'api_messages_theme', methods: ['POST'])]
    public function setConversationTheme(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $data = json_decode($request->getContent(), true);
            $theme = $data['theme'] ?? '#0084FF';

            $conn = $this->em->getConnection();
            $conn->executeStatement(
                "INSERT INTO conversation_theme (conversation_id, theme) VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE theme = ?",
                [$id, $theme, $theme]
            );

            return new JsonResponse(['success' => true, 'theme' => $theme]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/nickname', name: 'api_messages_nickname', methods: ['POST'])]
    public function setNickname(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $data = json_decode($request->getContent(), true);
            $nickname = trim($data['nickname'] ?? '');

            $otherUser = $conversation->getOtherUser($user);
            if (!$otherUser) return new JsonResponse(['error' => 'User not found'], 404);

            $conn = $this->em->getConnection();
            $conn->executeStatement(
                "INSERT INTO friend_nickname (user_id, friend_id, nickname) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE nickname = ?",
                [$user->getId(), $otherUser->getId(), $nickname, $nickname]
            );

            return new JsonResponse(['success' => true, 'nickname' => $nickname]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/mute', name: 'api_messages_mute', methods: ['POST'])]
    public function toggleMute(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $conn = $this->em->getConnection();
            $existing = $conn->fetchOne(
                "SELECT id, is_muted FROM conversation_settings WHERE conversation_id = ? AND user_id = ?",
                [$id, $user->getId()]
            );

            $muted = false;
            if ($existing) {
                $muted = !(int)$existing['is_muted'];
                $conn->executeStatement(
                    "UPDATE conversation_settings SET is_muted = ? WHERE id = ?",
                    [$muted ? 1 : 0, $existing['id']]
                );
            } else {
                $muted = true;
                $conn->executeStatement(
                    "INSERT INTO conversation_settings (conversation_id, user_id, is_muted) VALUES (?, ?, 1)",
                    [$id, $user->getId()]
                );
            }

            return new JsonResponse(['success' => true, 'muted' => $muted]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/archive', name: 'api_messages_archive', methods: ['POST'])]
    public function toggleArchive(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $conn = $this->em->getConnection();
            $existing = $conn->fetchOne(
                "SELECT id, is_archived FROM conversation_settings WHERE conversation_id = ? AND user_id = ?",
                [$id, $user->getId()]
            );

            $archived = false;
            if ($existing) {
                $archived = !(int)$existing['is_archived'];
                $conn->executeStatement(
                    "UPDATE conversation_settings SET is_archived = ? WHERE id = ?",
                    [$archived ? 1 : 0, $existing['id']]
                );
            } else {
                $archived = true;
                $conn->executeStatement(
                    "INSERT INTO conversation_settings (conversation_id, user_id, is_archived) VALUES (?, ?, 1)",
                    [$id, $user->getId()]
                );
            }

            return new JsonResponse(['success' => true, 'archived' => $archived]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/archived', name: 'api_messages_archived', methods: ['GET'])]
    public function getArchivedConversations(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conn = $this->em->getConnection();
            
            $sql = 'SELECT c.id 
                    FROM conversation c
                    INNER JOIN conversation_participant cp ON c.id = cp.conversation_id AND cp.user_id = :userId
                    INNER JOIN conversation_settings cs ON c.id = cs.conversation_id AND cs.user_id = :userId
                    WHERE cs.is_archived = 1
                    AND (cs.is_deleted IS NULL OR cs.is_deleted = 0)
                    ORDER BY c.updated_at DESC';
            
            $results = $conn->executeQuery($sql, ['userId' => $user->getId()])->fetchAllAssociative();
            
            $conversations = [];
            foreach ($results as $row) {
                $conv = $this->em->getRepository(Conversation::class)->find($row['id']);
                if ($conv) {
                    $conversations[] = $this->messageService->getConversationForApi($conv, $user);
                }
            }

            return new JsonResponse(['conversations' => $conversations]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/unarchive', name: 'api_messages_unarchive', methods: ['POST'])]
    public function unarchiveConversation(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $conn = $this->em->getConnection();
            $existing = $conn->fetchOne(
                "SELECT id FROM conversation_settings WHERE conversation_id = ? AND user_id = ?",
                [$id, $user->getId()]
            );

            if ($existing) {
                $conn->executeStatement(
                    "UPDATE conversation_settings SET is_archived = 0 WHERE id = ?",
                    [$existing]
                );
            }

            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function deleteConversation(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) return new JsonResponse(['error' => 'Not found'], 404);

            $conn = $this->em->getConnection();

            $conn->executeStatement(
                "UPDATE conversation_settings SET is_deleted = 1 WHERE conversation_id = ? AND user_id = ?",
                [$id, $user->getId()]
            );

            return new JsonResponse(['success' => true, 'message' => 'Conversation archived']);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
