<?php

namespace App\Service;

use App\Entity\FriendRequest;
use App\Entity\User;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\EntityManagerInterface;

class FriendService
{
    public function __construct(
        private EntityManagerInterface $em,
        private FriendRequestRepository $friendRequestRepository,
    ) {}

    public function sendFriendRequest(User $sender, User $receiver): array
    {
        if ($sender->getId() === $receiver->getId()) {
            return ['success' => false, 'error' => 'Vous ne pouvez pas vous ajouter vous-même'];
        }

        if ($this->areFriends($sender, $receiver)) {
            return ['success' => false, 'error' => 'Vous êtes déjà amis'];
        }

        $existing = $this->friendRequestRepository->findExistingRequest($sender, $receiver);
        if ($existing) {
            return ['success' => false, 'error' => 'Demande en attente de réponse'];
        }

        $reverseRequest = $this->friendRequestRepository->findExistingRequest($receiver, $sender);
        if ($reverseRequest) {
            $reverseRequest->accept();
            $this->em->flush();
            return ['success' => true, 'accepted' => true, 'message' => 'Demande acceptée ! Vous êtes maintenant amis.'];
        }

        $request = new FriendRequest();
        $request->setSender($sender);
        $request->setReceiver($receiver);
        $request->setStatus(FriendRequest::STATUS_PENDING);

        $this->em->persist($request);
        $this->em->flush();

        return ['success' => true, 'accepted' => false, 'message' => 'Demande envoyée'];
    }

    public function acceptFriendRequest(FriendRequest $request, User $user): array
    {
        if ($request->getReceiver()->getId() !== $user->getId()) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        if (!$request->isPending()) {
            return ['success' => false, 'error' => 'Demande déjà traitée'];
        }

        $request->accept();
        $this->em->flush();

        return ['success' => true, 'message' => 'Demande acceptée ! Vous êtes maintenant amis.'];
    }

    public function rejectFriendRequest(FriendRequest $request, User $user): array
    {
        if ($request->getReceiver()->getId() !== $user->getId()) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        if (!$request->isPending()) {
            return ['success' => false, 'error' => 'Demande déjà traitée'];
        }

        $request->reject();
        $this->em->flush();

        return ['success' => true, 'message' => 'Demande refusée.'];
    }

    public function cancelFriendRequest(FriendRequest $request, User $user): array
    {
        if ($request->getSender()->getId() !== $user->getId()) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        if (!$request->isPending()) {
            return ['success' => false, 'error' => 'Demande déjà traitée'];
        }

        $this->em->remove($request);
        $this->em->flush();

        return ['success' => true, 'message' => 'Demande annulée.'];
    }

    public function removeFriend(User $user1, User $user2): array
    {
        $friendships = $this->em->getRepository(FriendRequest::class)
            ->createQueryBuilder('fr')
            ->andWhere('((fr.sender = :user1 AND fr.receiver = :user2) OR (fr.sender = :user2 AND fr.receiver = :user1))')
            ->andWhere('fr.status = :status')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->setParameter('status', FriendRequest::STATUS_ACCEPTED)
            ->getQuery()
            ->getResult();

        if (empty($friendships)) {
            return ['success' => false, 'error' => 'Vous n\'êtes pas amis'];
        }

        foreach ($friendships as $friendship) {
            $this->em->remove($friendship);
        }
        $this->em->flush();

        return ['success' => true, 'message' => 'Ami supprimé de votre liste.'];
    }

    public function areFriends(User $user1, User $user2): bool
    {
        return $this->friendRequestRepository->areFriends($user1, $user2);
    }

    public function getFriendshipStatus(User $user, User $other): string
    {
        if ($this->areFriends($user, $other)) {
            return 'friends';
        }

        $request = $this->friendRequestRepository->findExistingRequest($user, $other);
        if ($request) {
            return $request->getSender()->getId() === $user->getId() ? 'sent' : 'received';
        }

        return 'none';
    }

    public function getPendingRequests(User $user): array
    {
        return $this->friendRequestRepository->findPendingRequestsForUser($user);
    }

    public function getSentRequests(User $user): array
    {
        return $this->friendRequestRepository->findSentRequestsByUser($user);
    }

    public function getFriends(User $user): array
    {
        return $this->friendRequestRepository->findFriendshipsForUser($user);
    }

    public function getFriendIds(User $user): array
    {
        return $this->friendRequestRepository->getFriendIds($user);
    }

    public function getPendingCount(User $user): int
    {
        return $this->friendRequestRepository->countPendingForUser($user);
    }

    public function getRequestForApi(FriendRequest $request, User $currentUser): array
    {
        $isReceived = $request->getReceiver()->getId() === $currentUser->getId();
        $other = $isReceived ? $request->getSender() : $request->getReceiver();

        return [
            'id' => $request->getId(),
            'status' => $request->getStatus(),
            'isReceived' => $isReceived,
            'sender' => [
                'id' => $request->getSender()->getId(),
                'name' => $request->getSender()->getFullName(),
                'avatar' => $request->getSender()->getAvatar(),
            ],
            'receiver' => [
                'id' => $request->getReceiver()->getId(),
                'name' => $request->getReceiver()->getFullName(),
                'avatar' => $request->getReceiver()->getAvatar(),
            ],
            'otherUser' => [
                'id' => $other->getId(),
                'name' => $other->getFullName(),
                'avatar' => $other->getAvatar(),
            ],
            'createdAt' => $request->getCreatedAt()->format('c'),
            'respondedAt' => $request->getRespondedAt()?->format('c'),
        ];
    }

<<<<<<< HEAD
    public function blockUser(User $user, User $other): void
    {
        $this->removeFriend($user, $other);
        $this->em->getConnection()->executeStatement(
            "INSERT INTO blocked_users (user_id, blocked_id) VALUES (?, ?)",
            [$user->getId(), $other->getId()]
        );
    }

    public function unblockUser(User $user, User $other): void
    {
        $this->em->getConnection()->executeStatement(
            "DELETE FROM blocked_users WHERE user_id = ? AND blocked_id = ?",
            [$user->getId(), $other->getId()]
        );
    }

=======
>>>>>>> testsisi
    public function getFriendForApi(FriendRequest $friendship, User $currentUser): array
    {
        $friend = $friendship->getSender()->getId() === $currentUser->getId()
            ? $friendship->getReceiver()
            : $friendship->getSender();

        return [
            'id' => $friend->getId(),
            'name' => $friend->getFullName(),
            'avatar' => $friend->getAvatar(),
            'friendshipId' => $friendship->getId(),
            'friendsSince' => $friendship->getRespondedAt()?->format('c'),
        ];
    }
}
