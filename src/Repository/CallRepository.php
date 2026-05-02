<?php

namespace App\Repository;

use App\Entity\Call;
use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Call::class);
    }

    public function findByConversation(Conversation $conversation, int $limit = 20): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user, int $limit = 50): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.caller = :user OR c.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findMissedByUser(User $user, bool $asCaller = false): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.status = :status');
        
        if ($asCaller) {
            $qb->andWhere('c.caller = :user');
        } else {
            $qb->andWhere('c.receiver = :user');
        }
        
        return $qb->andWhere('c.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('status', Call::STATUS_MISSED)
            ->setParameter('statuses', [Call::STATUS_MISSED, Call::STATUS_NO_ANSWER])
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMissedCount(User $user): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.receiver = :user')
            ->andWhere('c.status IN (:statuses)')
            ->andWhere('c.createdAt > :since')
            ->setParameter('user', $user)
            ->setParameter('statuses', [Call::STATUS_MISSED, Call::STATUS_NO_ANSWER])
            ->setParameter('since', new \DateTime('-24 hours'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
