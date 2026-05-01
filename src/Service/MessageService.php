<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\ConversationParticipant;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\ConversationParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessageService
{
    private ?HubInterface $hub = null;

    public function __construct(
        private EntityManagerInterface $em,
        private ConversationRepository $conversationRepo,
        private MessageRepository $messageRepo,
        private ConversationParticipantRepository $participantRepo,
        private UserRepository $userRepo,
    ) {}

    public function setMercureHub(?HubInterface $hub): void
    {
        $this->hub = $hub;
    }

    public function getUserConversations(User $user): array
    {
        return $this->conversationRepo->findByUser($user);
    }

    public function getOrCreatePrivateConversation(User $user1, User $user2): Conversation
    {
        $existing = $this->conversationRepo->findPrivateConversation($user1, $user2);
        if ($existing) {
            return $existing;
        }

        $conversation = new Conversation();
        $conversation->setType('private');
        $conversation->setCreatedBy($user1);
        $conversation->setCreatedAt(new \DateTime());
        $conversation->setUpdatedAt(new \DateTime());
        
        $this->em->persist($conversation);
        $this->em->flush();
        
        $participant1 = new ConversationParticipant();
        $participant1->setConversation($conversation);
        $participant1->setUser($user1);
        $participant1->setJoinedAt(new \DateTime());
        $participant1->setUnreadCount(0);
        
        $participant2 = new ConversationParticipant();
        $participant2->setConversation($conversation);
        $participant2->setUser($user2);
        $participant2->setJoinedAt(new \DateTime());
        $participant2->setUnreadCount(0);
        
        $this->em->persist($participant1);
        $this->em->persist($participant2);
        $this->em->flush();
        
        return $conversation;
    }

    public function createGroupConversation(string $name, User $creator, ?string $image = null): Conversation
    {
        $conversation = new Conversation();
        $conversation->setType('group');
        $conversation->setName($name);
        $conversation->setImage($image);
        $conversation->setCreatedBy($creator);
        $conversation->setCreatedAt(new \DateTime());
        $conversation->setUpdatedAt(new \DateTime());
        
        $this->em->persist($conversation);
        $this->em->flush();
        
        $participant = new ConversationParticipant();
        $participant->setConversation($conversation);
        $participant->setUser($creator);
        $participant->setJoinedAt(new \DateTime());
        $participant->setUnreadCount(0);
        
        $this->em->persist($participant);
        $this->em->flush();
        
        return $conversation;
    }

    public function addParticipant(Conversation $conversation, User $user): ConversationParticipant
    {
        $existing = $this->participantRepo->findOneBy([
            'conversation' => $conversation,
            'user' => $user,
        ]);
        
        if ($existing) {
            return $existing;
        }
        
        $participant = new ConversationParticipant();
        $participant->setConversation($conversation);
        $participant->setUser($user);
        $participant->setJoinedAt(new \DateTime());
        $participant->setUnreadCount(0);
        
        $this->em->persist($participant);
        $this->em->flush();
        
        return $participant;
    }

    public function removeParticipant(Conversation $conversation, User $user): void
    {
        $participant = $this->participantRepo->findOneBy([
            'conversation' => $conversation,
            'user' => $user,
        ]);

        if ($participant) {
            $this->em->remove($participant);
            $this->em->flush();
        }
    }

    public function sendMessage(Conversation $conversation, User $sender, string $content, ?string $image = null, ?string $audio = null, ?Message $replyTo = null): Message
    {
        $message = new Message();
        $message->setConversation($conversation);
        $message->setSender($sender);
        $message->setContent($content ?: '');
        $message->setStatus('sent');
        $message->setCreatedAt(new \DateTime());
        $message->setImage($image);
        $message->setAudio($audio);
        $message->setReplyTo($replyTo);
        
        $this->em->persist($message);
        $conversation->setUpdatedAt(new \DateTime());
        $this->em->flush();
        
        $this->publishNewMessage($message, $conversation);
        
        return $message;
    }

    private function publishNewMessage(Message $message, Conversation $conversation): void
    {
        if (!$this->hub) {
            return;
        }

        try {
            $sender = $message->getSender();
            $topic = 'conversation/' . $conversation->getId();
            
            $replyToData = null;
            $replyTo = $message->getReplyTo();
            if ($replyTo) {
                $replyToSender = $replyTo->getSender();
                $replyToData = [
                    'id' => $replyTo->getId(),
                    'content' => substr($replyTo->getContent(), 0, 100),
                    'senderName' => $replyToSender ? $replyToSender->getPrenom() : 'Utilisateur',
                ];
            }

            $messageData = [
                'type' => 'new_message',
                'message' => [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'image' => $message->getImage(),
                    'audio' => $message->getAudio(),
                    'status' => $message->getStatus(),
                    'createdAt' => $message->getCreatedAt()->format('c'),
                    'sender' => $sender ? [
                        'id' => $sender->getId(),
                        'name' => $sender->getPrenom() . ' ' . $sender->getNom(),
                        'avatar' => $sender->getAvatar(),
                    ] : null,
                    'replyTo' => $replyToData,
                ],
                'conversationId' => $conversation->getId(),
            ];

            $update = new Update(
                $topic,
                json_encode($messageData),
                true
            );

            $this->hub->publish($update);
        } catch (\Throwable $e) {
            error_log('Mercure publish error: ' . $e->getMessage());
        }
    }

    public function getMessages(Conversation $conversation, int $limit = 50, int $offset = 0): array
    {
        return $this->messageRepo->findByConversation($conversation->getId(), $limit, $offset);
    }
    
    public function getMessagesForApi(Conversation $conversation, User $currentUser, int $limit = 50, int $offset = 0): array
    {
        $conversationId = $conversation->getId();
        error_log('getMessagesForApi: convId=' . $conversationId . ', limit=' . $limit);
        
        $messages = $this->messageRepo->findByConversation($conversationId, $limit, $offset);
        error_log('getMessagesForApi: got ' . count($messages) . ' rows');
        
        $result = [];
        foreach ($messages as $row) {
            $senderId = isset($row['sender_id']) ? (int)$row['sender_id'] : null;
            $isMe = $senderId == $currentUser->getId();
            
            $messageType = $row['type'] ?? 'text';
            $metadata = null;
            
            if (in_array($messageType, ['SHARE_ACTIVITY', 'SHARE_CIRCUIT', 'STORY_REPLY']) && !empty($row['metadata'])) {
                $metadata = is_string($row['metadata']) ? json_decode($row['metadata'], true) : $row['metadata'];
            }
            
            $replyTo = null;
            if (!empty($row['reply_to'])) {
                $replyTo = [
                    'id' => (int)$row['reply_to'],
                    'content' => $row['reply_to_content'] ?? '',
                    'senderName' => trim(($row['reply_to_prenom'] ?? '') . ' ' . ($row['reply_to_nom'] ?? '')) ?: 'Utilisateur',
                ];
            }
            
            $result[] = [
                'id' => (int)($row['id'] ?? 0),
                'content' => $row['content'] ?? '',
                'type' => $messageType,
                'image' => $row['image'] ?? null,
                'audio' => $row['audio'] ?? null,
                'status' => $row['status'] ?? 'sent',
                'createdAt' => $row['created_at'] ?? null,
                'metadata' => $metadata,
                'replyTo' => $replyTo,
                'sender' => $senderId ? [
                    'id' => $senderId,
                    'name' => trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')),
                    'avatar' => $row['sender_avatar'] ?? null,
                ] : null,
                'isMe' => $isMe,
            ];
        }
        
        error_log('getMessagesForApi: returning ' . count($result) . ' messages');
        return $result;
    }

    public function markAsRead(Conversation $conversation, User $user): void
    {
        try {
            $conn = $this->em->getConnection();
            $conn->executeStatement(
                'UPDATE conversation_participant SET unread_count = 0, last_read_at = NOW() WHERE conversation_id = ? AND user_id = ?',
                [$conversation->getId(), $user->getId()]
            );
            
            $conn->executeStatement(
                'UPDATE message SET status = ?, read_at = NOW() WHERE conversation_id = ? AND sender_id != ? AND status != ?',
                [Message::STATUS_READ, $conversation->getId(), $user->getId(), Message::STATUS_READ]
            );
        } catch (\Throwable $e) {
            error_log('markAsRead error: ' . $e->getMessage());
        }
    }

    public function getTotalUnread(User $user): int
    {
        return $this->conversationRepo->getTotalUnread($user);
    }

    public function getOtherParticipant(Conversation $conversation, User $currentUser): ?User
    {
        try {
            $conn = $this->em->getConnection();
            $sql = 'SELECT cp.user_id FROM conversation_participant cp WHERE cp.conversation_id = ? AND cp.user_id != ? LIMIT 1';
            $userId = $conn->executeQuery($sql, [$conversation->getId(), $currentUser->getId()])->fetchOne();
            
            if ($userId) {
                return $this->em->getReference(User::class, $userId);
            }
        } catch (\Throwable $e) {
            error_log('getOtherParticipant error: ' . $e->getMessage());
        }
        
        return null;
    }

    public function getConversationForApi(Conversation $conversation, User $user): array
    {
        $otherUser = null;
        $nickname = null;
        try {
            $otherUser = $this->getOtherParticipant($conversation, $user);
            
            if ($otherUser) {
                $conn = $this->em->getConnection();
                $nicknameData = $conn->executeQuery(
                    'SELECT nickname FROM friend_nickname WHERE user_id = ? AND friend_id = ?',
                    [$user->getId(), $otherUser->getId()]
                )->fetchOne();
                $nickname = $nicknameData ?: null;
            }
        } catch (\Throwable $e) {
            // If we can't get the other participant, continue without it
        }
        
        $lastMsg = null;
        $lastMsgContent = null;
        $lastMsgCreatedAt = null;
        try {
            $conn = $this->em->getConnection();
            $lastMsgData = $conn->executeQuery(
                'SELECT content, created_at FROM message WHERE conversation_id = ? ORDER BY created_at DESC LIMIT 1',
                [$conversation->getId()]
            )->fetchAssociative();
            
            if ($lastMsgData) {
                $lastMsgContent = $lastMsgData['content'];
                $lastMsgCreatedAt = $lastMsgData['created_at'];
            }
        } catch (\Throwable $e) {
            // No messages yet
        }
        
        $unreadCount = 0;
        try {
            $conn = $this->em->getConnection();
            $unreadCountData = $conn->executeQuery(
                'SELECT unread_count FROM conversation_participant WHERE conversation_id = :convId AND user_id = :userId',
                ['convId' => $conversation->getId(), 'userId' => $user->getId()]
            )->fetchOne();
            $unreadCount = (int) ($unreadCountData ?: 0);
        } catch (\Throwable $e) {
            error_log('getConversationForApi unreadCount error: ' . $e->getMessage());
        }
        
        return [
            'id' => $conversation->getId(),
            'type' => $conversation->getType(),
            'name' => $conversation->getType() === 'group' ? $conversation->getName() : null,
            'image' => $conversation->getImage(),
            'nickname' => $nickname,
            'otherUser' => $otherUser ? [
                'id' => $otherUser->getId(),
                'name' => $otherUser->getPrenom() . ' ' . $otherUser->getNom(),
                'avatar' => $otherUser->getAvatar(),
            ] : null,
            'lastMessage' => $lastMsgContent !== null ? [
                'content' => $lastMsgContent,
                'createdAt' => $lastMsgCreatedAt,
            ] : null,
            'updatedAt' => $conversation->getUpdatedAt()?->format('c'),
            'unreadCount' => $unreadCount,
        ];
    }

    public function getLastMessage(Conversation $conversation): ?Message
    {
        return $this->messageRepo->findOneBy(
            ['conversation' => $conversation],
            ['createdAt' => 'DESC']
        );
    }

    public function searchMessages(Conversation $conversation, string $query): array
    {
        return $this->messageRepo->createQueryBuilder('m')
            ->where('m.conversation = :conv')
            ->andWhere('m.content LIKE :query')
            ->setParameter('conv', $conversation)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}