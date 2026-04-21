<?php

namespace App\Repository;

use App\Entity\MessageReaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageReaction::class);
    }

    public function findByMessageAndUser(int $messageId, int $userId): ?MessageReaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.message = :messageId')
            ->andWhere('r.user = :userId')
            ->setParameter('messageId', $messageId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByMessage(int $messageId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.message = :messageId')
            ->setParameter('messageId', $messageId)
            ->getQuery()
            ->getResult();
    }
}