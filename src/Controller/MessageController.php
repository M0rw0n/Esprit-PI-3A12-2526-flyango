<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\Conversation;
<<<<<<< HEAD
use App\Entity\ConversationParticipant;
=======
>>>>>>> testsisi
use App\Entity\Message;
use App\Entity\MessageReaction;
use App\Entity\User;
use App\Repository\CallRepository;
<<<<<<< HEAD
=======
use App\Repository\UserRepository;
>>>>>>> testsisi
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
<<<<<<< HEAD
        try {
            $token = $this->tokenStorage->getToken();
            $user = $token?->getUser();
            return $user instanceof User ? $user : null;
        } catch (\Throwable $e) {
            return null;
=======
        $token = $this->tokenStorage->getToken();
        return $token?->getUser() instanceof User ? $token->getUser() : null;
    }

    #[Route('/debug/messages/{convId}', name: 'api_messages_debug', methods: ['GET'])]
    public function debugMessages(int $convId): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) {
                return new JsonResponse(['error' => 'Unauthorized']);
            }
            
            $conn = $this->em->getConnection();
            $sql = "SELECT m.id, m.conversation_id, m.sender_id, m.content, m.status, m.created_at, m.read_at, m.image,
                           u.prenom, u.nom, u.profile_picture_path as sender_avatar
                    FROM message m
                    LEFT JOIN user u ON m.sender_id = u.id
                    WHERE m.conversation_id = " . (int)$convId . "
                    ORDER BY m.created_at ASC
                    LIMIT 50 OFFSET 0";
            
            error_log('Debug SQL: ' . $sql);
            
            $rawResult = $conn->executeQuery($sql)->fetchAllAssociative();
            
            error_log('Debug raw result count: ' . count($rawResult));
            
            $messages = [];
            foreach ($rawResult as $row) {
                $senderId = isset($row['sender_id']) ? (int)$row['sender_id'] : null;
                $isMe = $senderId == $user->getId();
                
                $messages[] = [
                    'id' => (int)$row['id'],
                    'content' => $row['content'] ?? '',
                    'image' => $row['image'] ?? null,
                    'audio' => $row['audio'] ?? null,
                    'status' => $row['status'] ?? 'sent',
                    'createdAt' => $row['created_at'] ?? null,
                    'sender' => $senderId ? [
                        'id' => $senderId,
                        'name' => trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')),
                        'avatar' => $row['sender_avatar'] ?? null,
                    ] : null,
                    'isMe' => $isMe,
                ];
            }
            
            return new JsonResponse([
                'conversation_id' => $convId,
                'user_id' => $user->getId(),
                'raw_query_count' => count($rawResult),
                'messages' => $messages,
            ]);
        } catch (\Throwable $e) {
            error_log('Debug error: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()]);
>>>>>>> testsisi
        }
    }

    #[Route('/conversations', name: 'api_messages_conversations', methods: ['GET'])]
    public function getConversations(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
<<<<<<< HEAD
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversations = $this->messageService->getUserConversations($user);
            $result = [];
            foreach ($conversations as $c) {
                if ($c instanceof Conversation) {
                    $result[] = $this->messageService->getConversationForApi($c, $user);
=======
            if (!$user) {
                return new JsonResponse(['error' => 'Unauthorized'], 401);
            }

            $conversations = $this->messageService->getUserConversations($user);
            
            $result = [];
            foreach ($conversations as $conv) {
                try {
                    $result[] = $this->messageService->getConversationForApi($conv, $user);
                } catch (\Throwable $e) {
                    continue;
>>>>>>> testsisi
                }
            }

            return new JsonResponse([
                'conversations' => $result,
                'totalUnread' => $this->messageService->getTotalUnread($user),
                'userId' => $user->getId(),
            ]);
        } catch (\Throwable $e) {
<<<<<<< HEAD
            error_log('getConversations error: ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
=======
            error_log('Conversations error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return new JsonResponse([
                'error' => 'Server error',
                'message' => $e->getMessage(),
                'type' => get_class($e),
            ], 500);
>>>>>>> testsisi
        }
    }

    #[Route('/conversation/{id}', name: 'api_messages_conversation', methods: ['GET'])]
<<<<<<< HEAD
    public function getConversation(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $limit = (int) $request->query->get('limit', 50);
            $offset = (int) $request->query->get('offset',0);

            $conversation = $this->em->getReference(Conversation::class, $id);
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
=======
    public function getConversation(int $id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) {
                return new JsonResponse(['error' => 'Conversation not found: ' . $id], 404);
            }

            $conn = $this->em->getConnection();
            $result = $conn->executeQuery(
                'SELECT COUNT(*) FROM conversation_participant WHERE conversation_id = ? AND user_id = ?',
                [$id, $user->getId()]
            )->fetchOne();

            if ((int)$result === 0) {
                return new JsonResponse(['error' => 'Access denied'], 403);
            }

            $this->messageService->markAsRead($conversation, $user);

            $sql = "SELECT m.id, m.conversation_id, m.sender_id, m.content, m.status, m.created_at, m.read_at, m.image, m.audio, m.video, m.reply_to,
                           u.prenom, u.nom, u.profile_picture_path as sender_avatar,
                           r.content as reply_content, r.sender_id as reply_sender_id,
                           ru.prenom as reply_prenom, ru.nom as reply_nom
                    FROM message m
                    LEFT JOIN user u ON m.sender_id = u.id
                    LEFT JOIN message r ON m.reply_to = r.id
                    LEFT JOIN user ru ON r.sender_id = ru.id
                    WHERE m.conversation_id = " . (int)$id . "
                    ORDER BY m.created_at ASC
                    LIMIT 50";
            
            $rawResult = $conn->executeQuery($sql)->fetchAllAssociative();
            
            $messagesData = [];
            if (empty($rawResult)) {
                $rawResult = [];
            }
            
            foreach ($rawResult as $row) {
                $senderId = isset($row['sender_id']) ? (int)$row['sender_id'] : null;
                $isMe = $senderId == $user->getId();
                
                $replyTo = null;
                if (!empty($row['reply_to']) && !empty($row['reply_content'])) {
                    $replyTo = [
                        'id' => (int)$row['reply_to'],
                        'content' => substr($row['reply_content'], 0, 50),
                        'sender' => $row['reply_sender_id'] ? [
                            'id' => (int)$row['reply_sender_id'],
                            'name' => trim(($row['reply_prenom'] ?? '') . ' ' . ($row['reply_nom'] ?? '')),
                        ] : null,
                    ];
                }
                
                $messagesData[] = [
                    'id' => (int)$row['id'],
                    'content' => $row['content'] ?? '',
                    'image' => $row['image'] ?? null,
                    'audio' => $row['audio'] ?? null,
                    'video' => $row['video'] ?? null,
                    'status' => $row['status'] ?? 'sent',
                    'createdAt' => $row['created_at'] ?? null,
                    'replyTo' => $replyTo,
                    'sender' => $senderId ? [
                        'id' => $senderId,
                        'name' => trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')),
                        'avatar' => $row['sender_avatar'] ?? null,
                    ] : null,
                    'isMe' => $isMe,
                ];
            }

            $convData = $this->messageService->getConversationForApi($conversation, $user);

            return new JsonResponse([
                'conversation' => $convData,
                'messages' => $messagesData,
            ]);
        } catch (\Exception $e) {
            error_log('Get conversation error: ' . $e->getMessage());
>>>>>>> testsisi
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/conversation/{id}/messages', name: 'api_messages_send', methods: ['POST'])]
    public function sendMessage(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
<<<<<<< HEAD
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $conversation = $this->em->getReference(Conversation::class, $id);
            if (!$conversation) return new JsonResponse(['error' => 'Conversation not found'], 404);

            $data = json_decode($request->getContent(), true) ?: $request->request->all();
            $content = $data['content'] ?? '';
            $replyToId = $data['replyTo'] ?? null;

            if (!$content && !$request->files->get('image') && !$request->files->get('audio')) {
                return new JsonResponse(['error' => 'Empty message'], 400);
            }

=======
            if (!$user) {
                return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
            }

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) {
                return new JsonResponse(['success' => false, 'error' => 'Conversation non trouvée'], 404);
            }

            $conn = $this->em->getConnection();
            $result = $conn->executeQuery(
                'SELECT COUNT(*) FROM conversation_participant WHERE conversation_id = ? AND user_id = ?',
                [$id, $user->getId()]
            )->fetchOne();

            if ((int)$result === 0) {
                return new JsonResponse(['success' => false, 'error' => 'Vous n\'êtes pas participant'], 403);
            }

            $content = $request->request->get('content', '');
            $replyToId = $request->request->get('reply_to');
            
            if (empty($content)) {
                $data = json_decode($request->getContent(), true);
                if ($data) {
                    $content = $data['content'] ?? '';
                    $replyToId = $data['reply_to'] ?? $replyToId;
                }
            }
            
>>>>>>> testsisi
            $replyTo = null;
            if ($replyToId) {
                $replyTo = $this->em->getRepository(Message::class)->find($replyToId);
            }
<<<<<<< HEAD

            $message = $this->messageService->sendMessage($conversation, $user, $content, null, null, $replyTo);
            
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

            $conversation = $this->em->getReference(Conversation::class, $convId);
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

            $conversation = $this->em->getReference(Conversation::class, $convId);
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
=======
            
            if (!$content || trim($content) === '') {
                return new JsonResponse(['success' => false, 'error' => 'Message vide'], 400);
            }

            $message = $this->messageService->sendMessage($conversation, $user, trim($content), null, null, $replyTo);
            
            return new JsonResponse([
                'success' => true,
                'message' => [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'createdAt' => $message->getCreatedAt()->format('c'),
                ],
            ]);
        } catch (\Throwable $e) {
            error_log('Send message error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur serveur',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function publishToMercure(string $topic, array $data): void
    {
        if (!$this->hub) {
            return;
        }

        try {
            $update = new Update($topic, json_encode($data));
            $this->hub->publish($update);
        } catch (\Throwable $e) {
            error_log('Mercure publish error: ' . $e->getMessage());
        }
    }

    #[Route('/conversation/{id}/read', name: 'api_messages_read', methods: ['POST'])]
    public function markRead(int $id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($id);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $this->messageService->markAsRead($conversation, $user);

        // Notify other participants via Mercure
        $this->publishToMercure('conversation/' . $id, [
            'type' => 'message_read',
            'conversationId' => $id,
            'readerId' => $user->getId()
        ]);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/unread-count', name: 'api_messages_unread', methods: ['GET'])]
    public function getUnreadCount(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return new JsonResponse([
            'total' => $this->messageService->getTotalUnread($user),
        ]);
    }

    #[Route('/ping', name: 'api_user_ping', methods: ['POST'])]
    public function ping(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $user->setLastActiveAt(new \DateTime());
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/start/{userId}', name: 'api_messages_start', methods: ['POST'])]
    public function startConversation(int $userId): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) {
                return new JsonResponse(['error' => 'Unauthorized', 'message' => 'User not authenticated'], 401);
            }

            $otherUser = $this->em->getRepository(User::class)->find($userId);
            if (!$otherUser) {
                return new JsonResponse(['error' => 'User not found', 'userId' => $userId], 404);
            }

            $conversation = $this->messageService->getOrCreatePrivateConversation($user, $otherUser);
            
            if (!$conversation || !$conversation->getId()) {
                return new JsonResponse(['error' => 'Failed to create conversation'], 500);
            }

            $convData = $this->messageService->getConversationForApi($conversation, $user);

            return new JsonResponse([
                'success' => true,
                'conversationId' => $conversation->getId(),
                'conversation' => $convData,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/users', name: 'api_messages_users', methods: ['GET'])]
    public function searchUsers(Request $request, UserRepository $userRepo): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return new JsonResponse(['users' => []]);
        }
        
        $users = $userRepo->searchUsers($query, $user->getId(), 20);

        $result = array_map(fn($u) => [
            'id' => $u->getId(),
            'name' => $u->getFullName(),
            'avatar' => $u->getAvatar(),
        ], $users);

        return new JsonResponse(['users' => $result]);
    }
    
    #[Route('/users/search', name: 'api_users_search', methods: ['GET'])]
    public function searchAllUsers(Request $request, UserRepository $userRepo): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return new JsonResponse(['users' => [], 'count' => 0]);
        }
        
        $users = $userRepo->searchUsers($query, $user->getId(), 10);

        $result = array_map(fn($u) => [
            'id' => $u->getId(),
            'name' => $u->getFullName(),
            'email' => $u->getEmail(),
            'avatar' => $u->getAvatar(),
            'firstName' => $u->getPrenom(),
            'lastName' => $u->getNom(),
        ], $users);

        return new JsonResponse([
            'users' => $result,
            'count' => count($result),
            'query' => $query
        ]);
    }

    #[Route('/upload-image', name: 'api_messages_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversationId = $request->request->get('conversation_id');
        if (!$conversationId) {
            return new JsonResponse(['error' => 'Conversation ID required'], 400);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $isParticipant = false;
        foreach ($conversation->getParticipants() as $p) {
            if ($p->getUser()->getId() === $user->getId()) {
                $isParticipant = true;
                break;
            }
        }

        if (!$isParticipant) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $uploadedFile = $request->files->get('image');
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'No image provided'], 400);
        }

        $originalName = $uploadedFile->getClientOriginalName();
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        if (!$ext) $ext = 'jpg';

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/messages';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '.' . $ext;
        $uploadedFile->move($uploadDir, $filename);
        $imagePath = '/uploads/messages/' . $filename;

        $message = $this->messageService->sendMessage($conversation, $user, '', $imagePath);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'image' => $imagePath,
                'createdAt' => $message->getCreatedAt()->format('c'),
            ],
        ]);
    }

    #[Route('/upload-audio', name: 'api_messages_upload_audio', methods: ['POST'])]
    public function uploadAudio(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversationId = $request->request->get('conversation_id');
        if (!$conversationId) {
            return new JsonResponse(['error' => 'Conversation ID required'], 400);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $uploadedFile = $request->files->get('audio');
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'No audio provided'], 400);
        }

        $ext = $uploadedFile->getClientOriginalExtension() ?: 'webm';
        if (!in_array(strtolower($ext), ['webm', 'mp3', 'wav', 'ogg'])) {
            $ext = 'webm';
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/messages/audio';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = 'voice_' . uniqid() . '.' . $ext;
        $uploadedFile->move($uploadDir, $filename);
        $audioPath = '/uploads/messages/audio/' . $filename;

        $message = $this->messageService->sendMessage($conversation, $user, '🎤 Message vocal', null, $audioPath);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'audio' => $audioPath,
                'content' => '🎤 Message vocal',
                'createdAt' => $message->getCreatedAt()->format('c'),
            ],
        ]);
    }

    #[Route('/upload-video', name: 'api_messages_upload_video', methods: ['POST'])]
    public function uploadVideo(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversationId = $request->request->get('conversation_id');
        if (!$conversationId) {
            return new JsonResponse(['error' => 'Conversation ID required'], 400);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $uploadedFile = $request->files->get('video');
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'No video provided'], 400);
        }

        $originalName = $uploadedFile->getClientOriginalName();
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        if (!$ext) $ext = 'mp4';

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/messages/videos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '.' . $ext;
        $uploadedFile->move($uploadDir, $filename);
        $videoPath = '/uploads/messages/videos/' . $filename;

        $message = $this->messageService->sendMessage($conversation, $user, '🎥 Vidéo', null, null, null, $videoPath);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'video' => $videoPath,
                'createdAt' => $message->getCreatedAt()->format('c'),
            ],
        ]);
    }

    #[Route('/conversation/{id}/typing', name: 'api_messages_typing', methods: ['POST'])]
    public function typingIndicator(int $id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // Notify other participants via Mercure
        $this->publishToMercure('conversation/' . $id, [
            'type' => 'typing',
            'conversationId' => $id,
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getPrenom() . ' ' . $user->getNom()
            ]
        ]);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/conversation/{id}/typing', name: 'api_messages_get_typing', methods: ['GET'])]
    public function getTypingIndicator(int $id): JsonResponse
    {
        return new JsonResponse(['typing' => []]);
    }

    #[Route('/conversation/{id}/last', name: 'api_messages_last', methods: ['GET'])]
    public function getLastMessage(int $id): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) {
                return new JsonResponse(['error' => 'Unauthorized'], 401);
            }

            $conversation = $this->em->getRepository(Conversation::class)->find($id);
            if (!$conversation) {
                return new JsonResponse(['lastId' => 0, 'message' => null]);
            }

            $lastMessage = $this->messageService->getLastMessage($conversation);
            
            if (!$lastMessage) {
                return new JsonResponse(['lastId' => 0, 'message' => null]);
            }
            
            $sender = $lastMessage->getSender();
            
            return new JsonResponse([
                'lastId' => $lastMessage->getId(),
                'message' => [
                    'id' => $lastMessage->getId(),
                    'content' => $lastMessage->getContent(),
                    'senderId' => $sender ? $sender->getId() : null,
                    'isMe' => $sender ? ($sender->getId() === $user->getId()) : false,
                ],
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['lastId' => 0, 'message' => null, 'error' => $e->getMessage()]);
        }
    }

    #[Route('/conversation/{id}/search', name: 'api_messages_search', methods: ['GET'])]
    public function searchMessages(Request $request, int $id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $query = $request->query->get('q', '');
        if (strlen($query) < 2) {
            return new JsonResponse(['messages' => []]);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($id);
        if (!$conversation) {
            return new JsonResponse(['messages' => []]);
        }

        $messages = $this->messageService->searchMessages($conversation, $query);

        return new JsonResponse([
            'messages' => array_map(fn($m) => [
                'id' => $m->getId(),
                'content' => $m->getContent(),
                'createdAt' => $m->getCreatedAt()->format('c'),
                'sender' => [
                    'id' => $m->getSender()->getId(),
                    'name' => $m->getSender()->getPrenom() . ' ' . $m->getSender()->getNom(),
                ],
            ], $messages),
        ]);
    }

    #[Route('/conversation/{id}/mute', name: 'api_messages_mute', methods: ['POST'])]
    public function muteConversation(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) return new JsonResponse(['success' => false], 401);

        $participant = $this->em->getRepository(\App\Entity\ConversationParticipant::class)->findOneBy([
            'conversation' => $id,
            'user' => $user
        ]);

        if ($participant) {
            $data = json_decode($request->getContent(), true);
            $mute = $data['mute'] ?? true;
            $participant->setIsMuted($mute);
            $this->em->flush();
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/conversation/{id}/archive', name: 'api_messages_archive', methods: ['POST'])]
    public function archiveConversation(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) return new JsonResponse(['success' => false], 401);

        $participant = $this->em->getRepository(\App\Entity\ConversationParticipant::class)->findOneBy([
            'conversation' => $id,
            'user' => $user
        ]);

        if ($participant) {
            $data = json_decode($request->getContent(), true);
            $archive = $data['archive'] ?? true;
            $participant->setIsArchived($archive);
            $this->em->flush();
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/delete/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function deleteMessage(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $message = $this->em->getRepository(Message::class)->find($id);
        if (!$message) {
            return new JsonResponse(['error' => 'Message not found'], 404);
        }

        if ($message->getSender()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $forAll = $request->query->get('forAll') === 'true';
        $conversationId = $message->getConversation()->getId();

        if ($forAll) {
            // Delete for all: update content to "Message deleted"
            $message->setContent('🚫 Ce message a été supprimé');
            $message->setImage(null);
            $message->setAudio(null);
            $message->setVideo(null);
            $this->em->flush();

            // Notify via Mercure
            $this->publishToMercure('conversation/' . $conversationId, [
                'type' => 'message_deleted',
                'messageId' => $id,
                'conversationId' => $conversationId
            ]);
        } else {
            // Just delete from DB (or we could have a deleted_by field for individual deletion)
            $this->em->remove($message);
            $this->em->flush();
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/react', name: 'api_messages_react', methods: ['POST'])]
    public function addReaction(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $message = $this->em->getRepository(Message::class)->find($id);
        if (!$message) {
            return new JsonResponse(['error' => 'Message non trouvé'], 404);
        }

        $emoji = $request->request->get('emoji', '👍');
        if (!in_array($emoji, ['👍', '❤️', '😂', '😮', '😢', '😡'])) {
            $emoji = '👍';
        }

        $existingReaction = $this->em->getRepository(MessageReaction::class)->findOneBy([
            'message' => $message,
            'user' => $user,
        ]);

        if ($existingReaction) {
            if ($existingReaction->getEmoji() === $emoji) {
                $this->em->remove($existingReaction);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'action' => 'removed']);
            } else {
                $existingReaction->setEmoji($emoji);
                $this->em->flush();
                return new JsonResponse(['success' => true, 'action' => 'updated']);
            }
        }

        $reaction = new MessageReaction();
        $reaction->setMessage($message);
        $reaction->setUser($user);
        $reaction->setEmoji($emoji);
        
        $this->em->persist($reaction);
        $this->em->flush();

        return new JsonResponse(['success' => true, 'action' => 'added']);
    }

    #[Route('/{id}/reactions', name: 'api_messages_get_reactions', methods: ['GET'])]
    public function getReactions(int $id): JsonResponse
    {
        $message = $this->em->getRepository(Message::class)->find($id);
        if (!$message) {
            return new JsonResponse(['error' => 'Message non trouvé'], 404);
        }

        $reactions = $this->em->getRepository(MessageReaction::class)->findBy(['message' => $message]);
        
        $grouped = [];
        foreach ($reactions as $r) {
>>>>>>> testsisi
            $emoji = $r->getEmoji();
            if (!isset($grouped[$emoji])) {
                $grouped[$emoji] = ['emoji' => $emoji, 'count' => 0, 'users' => []];
            }
            $grouped[$emoji]['count']++;
<<<<<<< HEAD
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

            $conversation = $this->em->getReference(Conversation::class, $id);
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

            $conversation = $this->em->getReference(Conversation::class, $id);
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
=======
            $grouped[$emoji]['users'][] = [
                'id' => $r->getUser()->getId(),
                'name' => $r->getUser()->getPrenom() . ' ' . $r->getUser()->getNom(),
            ];
        }

        return new JsonResponse(['reactions' => array_values($grouped)]);
    }

    #[Route('/send-gif', name: 'api_messages_send_gif', methods: ['POST'])]
    public function sendGif(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversationId = $request->request->get('conversation_id');
        $gifUrl = $request->request->get('gif_url');

        if (!$conversationId || !$gifUrl) {
            return new JsonResponse(['error' => 'Missing parameters'], 400);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $isParticipant = false;
        foreach ($conversation->getParticipants() as $p) {
            if ($p->getUser()->getId() === $user->getId()) {
                $isParticipant = true;
                break;
            }
        }

        if (!$isParticipant) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $message = $this->messageService->sendMessage($conversation, $user, 'GIF', $gifUrl);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'image' => $gifUrl,
                'createdAt' => $message->getCreatedAt()->format('c'),
            ],
        ]);
    }

    #[Route('/call/start/{userId}', name: 'api_messages_call_start', methods: ['POST'])]
    public function startCall(int $userId, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $otherUser = $this->em->getRepository(User::class)->find($userId);
        if (!$otherUser) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $callType = $data['type'] ?? 'audio';
        $offer = $data['offer'] ?? null;
        
        $callId = uniqid('call_');
        
        // Publish to Mercure for real-time signaling
        $this->publishToMercure('conversation/' . $userId, [
            'type' => 'call_signaling',
            'callId' => $callId,
            'offer' => $offer,
            'call' => [
                'id' => $callId,
                'from' => [
                    'id' => $user->getId(),
                    'name' => $user->getPrenom() . ' ' . $user->getNom(),
                ],
                'type' => $callType,
                'status' => 'calling',
            ]
        ]);

        return new JsonResponse([
            'success' => true,
            'callId' => $callId,
        ]);
    }

    #[Route('/call/{callId}/answer', name: 'api_messages_call_answer', methods: ['POST'])]
    public function answerCall(string $callId, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        $data = json_decode($request->getContent(), true);
        $answer = $data['answer'] ?? null;
        $toUserId = $data['toUserId'] ?? null; // We need to know who to send the answer to

        if ($toUserId) {
            $this->publishToMercure('conversation/' . $toUserId, [
                'type' => 'call_signaling',
                'callId' => $callId,
                'answer' => $answer
            ]);
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/call/signaling', name: 'api_messages_call_signaling', methods: ['GET'])]
    public function getSignaling(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['call' => null]);
        }

        // This is a simplified fallback for signaling if Mercure is not available
        // In a real app, you'd store pending signals in DB or Redis
        // For now, we return empty to stop the 404s
        return new JsonResponse(['call' => null]);
    }

    #[Route('/call/{callId}/ice', name: 'api_messages_call_ice', methods: ['POST'])]
    public function iceCandidate(string $callId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $candidate = $data['candidate'] ?? null;
        $toUserId = $data['toUserId'] ?? null;

        if ($toUserId) {
            $this->publishToMercure('conversation/' . $toUserId, [
                'type' => 'call_signaling',
                'callId' => $callId,
                'iceCandidate' => $candidate
            ]);
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/call/{callId}/accept', name: 'api_messages_call_accept', methods: ['POST'])]
    public function acceptCall(string $callId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'success' => true,
            'call' => [
                'id' => $callId,
                'status' => 'connected',
                'connectedAt' => (new \DateTime())->format('c'),
            ],
        ]);
    }

    #[Route('/call/{callId}/end', name: 'api_messages_call_end', methods: ['POST'])]
    public function endCall(string $callId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'success' => true,
            'call' => [
                'id' => $callId,
                'status' => 'ended',
                'endedAt' => (new \DateTime())->format('c'),
            ],
        ]);
    }

    #[Route('/call/initiate/{userId}', name: 'api_messages_call_initiate', methods: ['POST'])]
    public function initiateCall(int $userId, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $receiver = $this->em->getRepository(User::class)->find($userId);
        if (!$receiver) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $type = $request->request->get('type', Call::TYPE_AUDIO);
        
        $conversation = null;
        $existingConv = $this->messageService->getOrCreatePrivateConversation($user, $receiver);
        
        $call = new Call();
        $call->setCaller($user);
        $call->setReceiver($receiver);
        $call->setConversation($existingConv);
        $call->setType($type);
        $call->setStatus(Call::STATUS_MISSED);
        
        $this->em->persist($call);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'call' => [
                'id' => $call->getId(),
                'type' => $call->getType(),
                'status' => $call->getStatus(),
                'caller' => [
                    'id' => $user->getId(),
                    'name' => $user->getPrenom() . ' ' . $user->getNom(),
                ],
                'receiver' => [
                    'id' => $receiver->getId(),
                    'name' => $receiver->getPrenom() . ' ' . $receiver->getNom(),
                ],
                'createdAt' => $call->getCreatedAt()->format('c'),
            ],
        ]);
>>>>>>> testsisi
    }
}
