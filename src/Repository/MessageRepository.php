<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findByConversation(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        try {
            $conn = $this->getEntityManager()->getConnection();
            
            $limit = (int)$limit;
            $offset = (int)$offset;
            
            $sql = "SELECT m.id, m.conversation_id, m.sender_id, m.content, m.status, m.created_at, m.read_at, m.image,
                           u.prenom, u.nom, u.profile_picture_path as sender_avatar
                    FROM message m
                    LEFT JOIN user u ON m.sender_id = u.id
                    WHERE m.conversation_id = " . $conn->quote($conversationId, \PDO::PARAM_INT) . "
                    ORDER BY m.created_at ASC
                    LIMIT " . $conn->quote($limit, \PDO::PARAM_INT) . " OFFSET " . $conn->quote($offset, \PDO::PARAM_INT);
            
            error_log('MessageRepository SQL: ' . $sql);
            
            $result = $conn->executeQuery($sql)->fetchAllAssociative();
            
            error_log('MessageRepository: found ' . count($result) . ' rows for convId=' . $conversationId);
            
            return $result;
        } catch (\Throwable $e) {
            error_log('MessageRepository error: ' . $e->getMessage());
            return [];
        }
    }
}