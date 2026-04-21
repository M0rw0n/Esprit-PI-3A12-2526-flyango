<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findByUser(User $user): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('c', 'cp', 'u')
                ->innerJoin('c.participants', 'cp')
                ->innerJoin('cp.user', 'u')
                ->where('cp.user = :user')
                ->setParameter('user', $user)
                ->orderBy('c.updatedAt', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Throwable $e) {
            error_log('ConversationRepository findByUser error: ' . $e->getMessage());
            return $this->createQueryBuilder('c')
                ->where('c.id IN (
                    SELECT cp2.conversation_id FROM conversation_participant cp2 
                    WHERE cp2.user_id = :userId
                )')
                ->setParameter('userId', $user->getId())
                ->orderBy('c.updatedAt', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

    public function findPrivateConversation(User $user1, User $user2): ?Conversation
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = 'SELECT c.id 
                FROM conversation c
                INNER JOIN conversation_participant cp1 ON c.id = cp1.conversation_id AND cp1.user_id = :user1
                INNER JOIN conversation_participant cp2 ON c.id = cp2.conversation_id AND cp2.user_id = :user2
                WHERE c.type = :type
                LIMIT 1';
        
        $result = $conn->executeQuery($sql, [
            'user1' => $user1->getId(),
            'user2' => $user2->getId(),
            'type' => 'private'
        ])->fetchAssociative();
        
        if (!$result) {
            return null;
        }
        
        return $this->find($result['id']);
    }

    public function getTotalUnread(User $user): int
    {
        try {
            $conn = $this->getEntityManager()->getConnection();
            
            $sql = 'SELECT SUM(cp.unread_count) as total 
                    FROM conversation_participant cp 
                    WHERE cp.user_id = :userId';
            
            $result = $conn->executeQuery($sql, ['userId' => $user->getId()])->fetchAssociative();
            
            return (int) ($result['total'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function findByUserOptimized(User $user, int $limit = 50): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'cp', 'u')
            ->innerJoin('c.participants', 'cp')
            ->innerJoin('cp.user', 'u')
            ->where('cp.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
