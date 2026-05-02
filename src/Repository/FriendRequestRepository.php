<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FriendRequest>
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    public function save(FriendRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FriendRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPendingRequestsForUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.receiver = :user')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->orderBy('fr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSentRequestsByUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.sender = :user')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->orderBy('fr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFriendshipsForUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('(fr.sender = :user OR fr.receiver = :user)')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendRequest::STATUS_ACCEPTED)
            ->orderBy('fr.respondedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findExistingRequest(User $sender, User $receiver): ?FriendRequest
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.sender = :sender AND fr.receiver = :receiver')
            ->orWhere('fr.sender = :receiver AND fr.receiver = :sender')
            ->andWhere('fr.status = :status')
            ->setParameter('sender', $sender)
            ->setParameter('receiver', $receiver)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function areFriends(User $user1, User $user2): bool
    {
        $result = $this->createQueryBuilder('fr')
            ->select('COUNT(fr.id)')
            ->andWhere('((fr.sender = :user1 AND fr.receiver = :user2) OR (fr.sender = :user2 AND fr.receiver = :user1))')
            ->andWhere('fr.status = :status')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->setParameter('status', FriendRequest::STATUS_ACCEPTED)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    public function countPendingForUser(User $user): int
    {
        return (int) $this->createQueryBuilder('fr')
            ->select('COUNT(fr.id)')
            ->andWhere('fr.receiver = :user')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFriendIds(User $user): array
    {
        $requests = $this->createQueryBuilder('fr')
            ->andWhere('(fr.sender = :user OR fr.receiver = :user)')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendRequest::STATUS_ACCEPTED)
            ->getQuery()
            ->getResult();

        $friendIds = [];
        foreach ($requests as $req) {
            if ($req->getSender()->getId() === $user->getId()) {
                $friendIds[] = $req->getReceiver()->getId();
            } else {
                $friendIds[] = $req->getSender()->getId();
            }
        }

        return $friendIds;
    }
}
