<?php

namespace App\Controller\Api;

use App\Entity\Activity;
use App\Entity\Circuit;
use App\Entity\Conversation;
use App\Entity\ConversationParticipant;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\CircuitRepository;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use App\Service\ShareService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/share')]
class ShareApiController extends AbstractController
{
    public function __construct(
        private ShareService $shareService,
        private ActivityRepository $activityRepository,
        private CircuitRepository $circuitRepository,
        private ConversationRepository $conversationRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    #[Route('/message', name: 'api_share_message', methods: ['POST'])]
    public function shareToMessage(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? null; // 'activity' or 'circuit'
        $itemId = $data['item_id'] ?? null;
        $conversationId = $data['conversation_id'] ?? null;

        if (!$type || !$itemId || !$conversationId) {
            return new JsonResponse(['success' => false, 'error' => 'Paramètres manquants'], 400);
        }

        $conversation = $this->conversationRepository->find($conversationId);
        if (!$conversation) {
            return new JsonResponse(['success' => false, 'error' => 'Conversation non trouvée'], 404);
        }

        // Check if user is participant
        $isParticipant = false;
        foreach ($conversation->getParticipants() as $participant) {
            if ($participant->getUser() === $user) {
                $isParticipant = true;
                break;
            }
        }

        if (!$isParticipant) {
            return new JsonResponse(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        if ($type === 'activity') {
            $activity = $this->activityRepository->find($itemId);
            if (!$activity) return new JsonResponse(['success' => false, 'error' => 'Activité non trouvée'], 404);
            $this->shareService->shareActivityToMessenger($activity, $conversation, $user);
        } elseif ($type === 'circuit') {
            $circuit = $this->circuitRepository->find($itemId);
            if (!$circuit) return new JsonResponse(['success' => false, 'error' => 'Circuit non trouvé'], 404);
            $this->shareService->shareCircuitToMessenger($circuit, $conversation, $user);
        } else {
            return new JsonResponse(['success' => false, 'error' => 'Type invalide'], 400);
        }

        // Update conversation timestamp
        $conversation->setUpdatedAt(new \DateTime());
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/forum', name: 'api_share_forum', methods: ['POST'])]
    public function shareToForum(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? null;
        $itemId = $data['item_id'] ?? null;

        if (!$type || !$itemId) {
            return new JsonResponse(['success' => false, 'error' => 'Paramètres manquants'], 400);
        }

        if ($type === 'activity') {
            $activity = $this->activityRepository->find($itemId);
            if (!$activity) return new JsonResponse(['success' => false, 'error' => 'Activité non trouvée'], 404);
            $post = $this->shareService->shareActivityToForum($activity, $user);
        } elseif ($type === 'circuit') {
            $circuit = $this->circuitRepository->find($itemId);
            if (!$circuit) return new JsonResponse(['success' => false, 'error' => 'Circuit non trouvé'], 404);
            $post = $this->shareService->shareCircuitToForum($circuit, $user);
        } else {
            return new JsonResponse(['success' => false, 'error' => 'Type invalide'], 400);
        }

        return new JsonResponse([
            'success' => true,
            'post_id' => $post->getId()
        ]);
    }

    #[Route('/get-conversations', name: 'api_share_get_conversations', methods: ['GET'])]
    public function getConversations(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $conversations = $this->conversationRepository->findAllForUser($user);
        
        $formatted = [];
        foreach ($conversations as $conv) {
            $otherUser = null;
            if ($conv->getType() === 'private') {
                foreach ($conv->getParticipants() as $p) {
                    if ($p->getUser() !== $user) {
                        $otherUser = [
                            'id' => $p->getUser()->getId(),
                            'name' => $p->getUser()->getFullName(),
                            'avatar' => $p->getUser()->getAvatar()
                        ];
                        break;
                    }
                }
            }

            $formatted[] = [
                'id' => $conv->getId(),
                'name' => $conv->getName(),
                'type' => $conv->getType(),
                'otherUser' => $otherUser
            ];
        }

        return new JsonResponse([
            'success' => true,
            'conversations' => $formatted
        ]);
    }

    #[Route('/search-users', name: 'api_share_search_users', methods: ['GET'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $q = $request->query->get('q', '');
        
        $qb = $this->userRepository->createQueryBuilder('u')
            ->where('u.id != :currentUser')
            ->setParameter('currentUser', $user->getId())
            ->setMaxResults(20);

        if ($q && strlen($q) >= 1) {
            $qb->andWhere('(u.email LIKE :q OR u.nom LIKE :q OR u.prenom LIKE :q)')
               ->setParameter('q', '%' . $q . '%');
        }

        $users = $qb->getQuery()->getResult();

        $formatted = [];
        foreach ($users as $u) {
            $formatted[] = [
                'id' => $u->getId(),
                'name' => $u->getFullName(),
                'avatar' => $u->getAvatar(),
                'email' => $u->getEmail()
            ];
        }

        return new JsonResponse([
            'success' => true,
            'users' => $formatted
        ]);
    }

    #[Route('/create-conversation', name: 'api_share_create_conversation', methods: ['POST'])]
    public function createConversation(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $friendId = $data['friend_id'] ?? null;

        if (!$friendId) {
            return new JsonResponse(['success' => false, 'error' => 'ID ami manquant'], 400);
        }

        $friend = $this->userRepository->find($friendId);
        if (!$friend) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        if ($friend->getId() === $user->getId()) {
            return new JsonResponse(['success' => false, 'error' => 'Vous ne pouvez pas créer une conversation avec vous-même'], 400);
        }

        // Check if conversation already exists
        $existingConv = $this->conversationRepository->findPrivateConversation($user, $friend);
        if ($existingConv) {
            return new JsonResponse([
                'success' => true,
                'conversation_id' => $existingConv->getId(),
                'name' => $existingConv->getName(),
                'otherUser' => [
                    'id' => $friend->getId(),
                    'name' => $friend->getFullName(),
                    'avatar' => $friend->getAvatar()
                ]
            ]);
        }

        // Create new conversation
        $conversation = new Conversation();
        $conversation->setName($friend->getFullName());
        $conversation->setType('private');
        $conversation->setCreatedBy($user);

        $participant1 = new ConversationParticipant();
        $participant1->setUser($user);
        $participant1->setConversation($conversation);

        $participant2 = new ConversationParticipant();
        $participant2->setUser($friend);
        $participant2->setConversation($conversation);

        $this->em->persist($conversation);
        $this->em->persist($participant1);
        $this->em->persist($participant2);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'conversation_id' => $conversation->getId(),
            'name' => $conversation->getName(),
            'otherUser' => [
                'id' => $friend->getId(),
                'name' => $friend->getFullName(),
                'avatar' => $friend->getAvatar()
            ]
        ]);
    }
}
