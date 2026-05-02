<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\User;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use App\Service\FriendService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api/friend')]
class FriendController extends AbstractController
{
    public function __construct(
        private FriendService $friendService,
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
        private FriendRequestRepository $friendRequestRepository,
        private UserRepository $userRepository,
    ) {}

    private function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        return $token?->getUser() instanceof User ? $token->getUser() : null;
    }

#[Route('/request/{userId}', name: 'api_friend_request', methods: ['POST'])]
    public function sendRequest(int $userId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $receiver = $this->userRepository->find($userId);
        if (!$receiver) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        $result = $this->friendService->sendFriendRequest($user, $receiver);

        if (!$result['success']) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse($result);
    }

    #[Route('/nickname', name: 'api_friend_nickname', methods: ['POST'])]
    public function setNickname(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $friendId = $data['userId'] ?? null;
        $nickname = $data['nickname'] ?? '';

        if (!$friendId) {
            return new JsonResponse(['success' => false, 'error' => 'ID utilisateur manquant'], 400);
        }

        $friend = $this->userRepository->find($friendId);
        if (!$friend) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        $tableName = 'friend_nickname';
        
        $existing = $this->em->getConnection()->fetchOne(
            "SELECT id FROM {$tableName} WHERE user_id = ? AND friend_id = ?",
            [$user->getId(), $friendId]
        );

        if ($existing) {
            $this->em->getConnection()->executeStatement(
                "UPDATE {$tableName} SET nickname = ? WHERE id = ?",
                [$nickname, $existing]
            );
        } else {
            $this->em->getConnection()->executeStatement(
                "INSERT INTO {$tableName} (user_id, friend_id, nickname) VALUES (?, ?, ?)",
                [$user->getId(), $friendId, $nickname]
            );
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/accept/{requestId}', name: 'api_friend_accept', methods: ['POST'])]
    public function acceptRequest(int $requestId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $request = $this->friendRequestRepository->find($requestId);
        if (!$request) {
            return new JsonResponse(['success' => false, 'error' => 'Demande non trouvée'], 404);
        }

        $result = $this->friendService->acceptFriendRequest($request, $user);

        if (!$result['success']) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    #[Route('/reject/{requestId}', name: 'api_friend_reject', methods: ['POST'])]
    public function rejectRequest(int $requestId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $request = $this->friendRequestRepository->find($requestId);
        if (!$request) {
            return new JsonResponse(['success' => false, 'error' => 'Demande non trouvée'], 404);
        }

        $result = $this->friendService->rejectFriendRequest($request, $user);

        if (!$result['success']) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    #[Route('/cancel/{requestId}', name: 'api_friend_cancel', methods: ['POST'])]
    public function cancelRequest(int $requestId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $request = $this->friendRequestRepository->find($requestId);
        if (!$request) {
            return new JsonResponse(['success' => false, 'error' => 'Demande non trouvée'], 404);
        }

        $result = $this->friendService->cancelFriendRequest($request, $user);

        if (!$result['success']) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    #[Route('/remove/{userId}', name: 'api_friend_remove', methods: ['POST'])]
    public function removeFriend(int $userId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $friend = $this->userRepository->find($userId);
        if (!$friend) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        $result = $this->friendService->removeFriend($user, $friend);

        if (!$result['success']) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    #[Route('/received', name: 'api_friend_received', methods: ['GET'])]
    public function getReceivedRequests(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $requests = $this->friendService->getPendingRequests($user);
        $data = array_map(fn($r) => $this->friendService->getRequestForApi($r, $user), $requests);

        return new JsonResponse([
            'requests' => $data,
            'count' => count($data),
        ]);
    }

    #[Route('/sent', name: 'api_friend_sent', methods: ['GET'])]
    public function getSentRequests(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $requests = $this->friendService->getSentRequests($user);
        $data = array_map(fn($r) => $this->friendService->getRequestForApi($r, $user), $requests);

        return new JsonResponse([
            'requests' => $data,
            'count' => count($data),
        ]);
    }

    #[Route('/list', name: 'api_friend_list', methods: ['GET'])]
    public function getFriends(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $friendships = $this->friendService->getFriends($user);
        $data = array_map(fn($f) => $this->friendService->getFriendForApi($f, $user), $friendships);

        return new JsonResponse([
            'friends' => $data,
            'count' => count($data),
        ]);
    }

    #[Route('/status/{userId}', name: 'api_friend_status', methods: ['GET'])]
    public function getStatus(int $userId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $other = $this->userRepository->find($userId);
        if (!$other) {
            return new JsonResponse(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }

        $status = $this->friendService->getFriendshipStatus($user, $other);

        return new JsonResponse([
            'status' => $status,
            'userId' => $userId,
        ]);
    }

    #[Route('/pending/count', name: 'api_friend_pending_count', methods: ['GET'])]
    public function getPendingCount(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'count' => $this->friendService->getPendingCount($user),
        ]);
    }

    #[Route('/ids', name: 'api_friend_ids', methods: ['GET'])]
    public function getFriendIds(): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        return new JsonResponse([
            'friendIds' => $this->friendService->getFriendIds($user),
        ]);
    }

    #[Route('/block/{userId}', name: 'api_friend_block', methods: ['POST'])]
    public function blockUser(int $userId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $other = $this->userRepository->find($userId);
        if (!$other) return new JsonResponse(['error' => 'Not found'], 404);

        $this->friendService->blockUser($user, $other);
        return new JsonResponse(['success' => true]);
    }

    #[Route('/unblock/{userId}', name: 'api_friend_unblock', methods: ['POST'])]
    public function unblockUser(int $userId): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

        $other = $this->userRepository->find($userId);
        if (!$other) return new JsonResponse(['error' => 'Not found'], 404);

        $this->friendService->unblockUser($user, $other);
        return new JsonResponse(['success' => true]);
    }

    #[Route('/search', name: 'api_friend_search', methods: ['GET'])]
    public function searchByName(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $name = $request->query->get('name', '');
        
        if (strlen($name) < 2) {
            return new JsonResponse(['users' => []]);
        }
        
        $users = $this->userRepository->searchUsers($name, $user->getId(), 10);
        
        $result = array_map(fn($u) => [
            'id' => $u->getId(),
            'name' => $u->getFullName(),
            'avatar' => $u->getAvatar(),
            'email' => $u->getEmail(),
        ], $users);

        return new JsonResponse(['users' => $result]);
    }
}
