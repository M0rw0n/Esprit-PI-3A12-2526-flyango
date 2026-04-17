<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Entity\User;
use App\Entity\ConversationParticipant;
use App\Repository\ForumPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ShareController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
        private ForumPostRepository $forumPostRepository,
    ) {}

    private function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        return $token?->getUser() instanceof User ? $token->getUser() : null;
    }

    #[Route('/api/share/to-conversation', name: 'api_share_to_conversation', methods: ['POST'])]
    public function shareToConversation(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        
        $conversationId = $data['conversation_id'] ?? null;
        $postId = $data['post_id'] ?? null;
        $message = $data['message'] ?? '';

        if (!$conversationId || !$postId) {
            return new JsonResponse(['success' => false, 'error' => 'Paramètres manquants'], 400);
        }

        $conn = $this->em->getConnection();
        
        $participant = $conn->executeQuery(
            'SELECT 1 FROM conversation_participant WHERE conversation_id = ? AND user_id = ? LIMIT 1',
            [$conversationId, $user->getId()]
        )->fetchAssociative();
        
        if (!$participant) {
            return new JsonResponse(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $post = $this->forumPostRepository->find($postId);
        if (!$post) {
            return new JsonResponse(['success' => false, 'error' => 'Post non trouvé'], 404);
        }

        $msg = new Message();
        $msg->setConversation($this->em->getReference(Conversation::class, $conversationId));
        $msg->setSender($user);
        $msg->setContent($message ?: 'Je partage un post avec vous');
        $msg->setForumPostId($postId);
        $msg->setStatus('sent');
        $msg->setCreatedAt(new \DateTime());

        $this->em->persist($msg);
        $conn->executeStatement(
            'UPDATE conversation SET updated_at = NOW() WHERE id = ?',
            [$conversationId]
        );
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'message_id' => $msg->getId(),
            'post' => [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
            ],
        ]);
    }

    #[Route('/api/share/get-conversations', name: 'api_share_get_conversations', methods: ['GET'])]
    public function getConversations(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        try {
            $conn = $this->em->getConnection();
            
            $sql = 'SELECT c.id, c.type, c.name, c.image,
                    u.id as user_id, u.prenom, u.nom, u.avatar
                    FROM conversation c
                    INNER JOIN conversation_participant cp ON c.id = cp.conversation_id
                    LEFT JOIN conversation_participant cp2 ON c.id = cp2.conversation_id AND cp2.user_id != :currentUserId
                    LEFT JOIN user u ON u.id = cp2.user_id
                    WHERE cp.user_id = :currentUserId
                    ORDER BY c.updated_at DESC
                    LIMIT 50';
            
            $results = $conn->executeQuery($sql, ['currentUserId' => $user->getId()])->fetchAllAssociative();
            
            $conversations = [];
            foreach ($results as $row) {
                $conversations[] = [
                    'id' => (int) $row['id'],
                    'type' => $row['type'],
                    'name' => $row['type'] === 'group' ? $row['name'] : null,
                    'otherUser' => $row['user_id'] ? [
                        'id' => (int) $row['user_id'],
                        'name' => trim($row['prenom'] . ' ' . $row['nom']),
                        'avatar' => $row['avatar'],
                    ] : null,
                ];
            }

            return new JsonResponse([
                'success' => true,
                'conversations' => $conversations,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/share/create-conversation', name: 'api_share_create_conversation', methods: ['POST'])]
    public function createConversation(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $userId = $data['user_id'] ?? null;

        if (!$userId) {
            return new JsonResponse(['success' => false, 'error' => 'ID utilisateur requis'], 400);
        }

        $otherUser = $this->em->find(User::class, $userId);
        if (!$otherUser) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        $conn = $this->em->getConnection();
        
        $existing = $conn->executeQuery(
            'SELECT c.id FROM conversation c
             INNER JOIN conversation_participant cp1 ON c.id = cp1.conversation_id AND cp1.user_id = ?
             INNER JOIN conversation_participant cp2 ON c.id = cp2.conversation_id AND cp2.user_id = ?
             WHERE c.type = ? LIMIT 1',
            [$user->getId(), $userId, 'private']
        )->fetchAssociative();

        if ($existing) {
            return new JsonResponse([
                'success' => true,
                'conversation_id' => (int) $existing['id'],
                'created' => false,
            ]);
        }

        $conversation = new Conversation();
        $conversation->setType('private');
        $conversation->setCreatedBy($user);
        $conversation->setCreatedAt(new \DateTime());
        $conversation->setUpdatedAt(new \DateTime());

        $this->em->persist($conversation);
        $this->em->flush();

        $p1 = new ConversationParticipant();
        $p1->setConversation($conversation);
        $p1->setUser($user);
        $p1->setJoinedAt(new \DateTime());
        $p1->setUnreadCount(0);

        $p2 = new ConversationParticipant();
        $p2->setConversation($conversation);
        $p2->setUser($otherUser);
        $p2->setJoinedAt(new \DateTime());
        $p2->setUnreadCount(0);

        $this->em->persist($p1);
        $this->em->persist($p2);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'conversation_id' => $conversation->getId(),
            'created' => true,
        ]);
    }

    #[Route('/api/share/get-post/{id}', name: 'api_share_get_post', methods: ['GET'])]
    public function getPost(int $id): JsonResponse
    {
        $post = $this->forumPostRepository->find($id);
        if (!$post) {
            return new JsonResponse(['success' => false, 'error' => 'Post non trouvé'], 404);
        }

        return new JsonResponse([
            'success' => true,
            'post' => [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => substr($post->getContent(), 0, 200),
                'author' => $post->getAuthor(),
                'url' => '/forum/' . $post->getId(),
            ],
        ]);
    }
}
