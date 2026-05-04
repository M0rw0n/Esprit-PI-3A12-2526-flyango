<?php

namespace App\Service;

<<<<<<< HEAD
use App\Entity\Call;
use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class CallService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ?HubInterface $hub = null,
=======
use Doctrine\ORM\EntityManagerInterface;

class CallService
{
    private array $activeCalls = [];
    private array $pendingCalls = [];

    public function __construct(
        private EntityManagerInterface $em,
>>>>>>> testsisi
    ) {}

    public function initiateCall(int $callerId, int $calleeId, int $conversationId, string $type = 'video'): array
    {
<<<<<<< HEAD
        $caller = $this->em->getRepository(User::class)->find($callerId);
        $callee = $this->em->getRepository(User::class)->find($calleeId);
        $conversation = $this->em->getReference(Conversation::class, $conversationId);

        if (!$caller || !$callee) {
            throw new \Exception('User not found');
        }

        $call = new Call();
        $call->setCaller($caller);
        $call->setReceiver($callee);
        $call->setConversation($conversation);
        $call->setType($type);
        $call->setStatus('pending');

        $this->em->persist($call);
        $this->em->flush();

        if ($this->hub) {
            $topic = 'user/' . $calleeId . '/calls';
            $update = new Update(
                $topic,
                json_encode([
                    'type' => 'incoming_call',
                    'callId' => $call->getId(),
                    'caller' => [
                        'id' => $caller->getId(),
                        'name' => $caller->getPrenom() . ' ' . $caller->getNom(),
                        'avatar' => $caller->getAvatar(),
                    ],
                    'callType' => $type,
                    'conversationId' => $conversationId,
                ]),
                true
            );
            $this->hub->publish($update);
        }

        return [
            'callId' => $call->getId(),
=======
        $callId = uniqid('call_');
        
        $this->activeCalls[$callId] = [
            'id' => $callId,
            'caller_id' => $callerId,
            'callee_id' => $calleeId,
            'conversation_id' => $conversationId,
            'type' => $type,
            'status' => 'pending',
            'created_at' => time(),
        ];

        return [
            'callId' => $callId,
>>>>>>> testsisi
            'callerId' => $callerId,
            'calleeId' => $calleeId,
            'type' => $type,
            'conversationId' => $conversationId,
        ];
    }

<<<<<<< HEAD
    public function acceptCall(int $callId, int $userId): array
    {
        $call = $this->em->find(Call::class, $callId);
        if (!$call) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        if ($call->getReceiver()->getId() !== $userId) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        $call->accept();
        $this->em->flush();

        if ($this->hub) {
            $topic = 'user/' . $call->getCaller()->getId() . '/calls';
            $update = new Update(
                $topic,
                json_encode([
                    'type' => 'call_accepted',
                    'callId' => $call->getId(),
                    'calleeId' => $userId,
                ]),
                true
            );
            $this->hub->publish($update);
        }

        return [
            'success' => true,
            'call' => [
                'id' => $call->getId(),
                'status' => $call->getStatus(),
            ],
        ];
    }

    public function rejectCall(int $callId, int $userId): array
    {
        $call = $this->em->find(Call::class, $callId);
        if (!$call) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        if ($call->getCaller()->getId() !== $userId && $call->getReceiver()->getId() !== $userId) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        $call->decline();
        $this->em->flush();

        if ($this->hub) {
            $otherUserId = $call->getCaller()->getId() === $userId ? $call->getReceiver()->getId() : $call->getCaller()->getId();
            $topic = 'user/' . $otherUserId . '/calls';
            $update = new Update(
                $topic,
                json_encode([
                    'type' => 'call_rejected',
                    'callId' => $call->getId(),
                    'by' => $userId,
                ]),
                true
            );
            $this->hub->publish($update);
        }
=======
    public function acceptCall(string $callId, int $userId): array
    {
        if (!isset($this->activeCalls[$callId])) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        $call = $this->activeCalls[$callId];
        
        if ($call['callee_id'] !== $userId) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        $call['status'] = 'active';
        $this->activeCalls[$callId] = $call;

        return [
            'success' => true,
            'call' => $call,
        ];
    }

    public function rejectCall(string $callId, int $userId): array
    {
        if (!isset($this->activeCalls[$callId])) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        $call = $this->activeCalls[$callId];
        
        if ($call['caller_id'] !== $userId && $call['callee_id'] !== $userId) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        unset($this->activeCalls[$callId]);
>>>>>>> testsisi

        return ['success' => true];
    }

<<<<<<< HEAD
    public function endCall(int $callId, int $userId): array
    {
        $call = $this->em->find(Call::class, $callId);
        if (!$call) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        $call->end();
        $this->em->flush();

        if ($this->hub) {
            $otherUserId = $call->getCaller()->getId() === $userId ? $call->getReceiver()->getId() : $call->getCaller()->getId();
            $topic = 'user/' . $otherUserId . '/calls';
            $update = new Update(
                $topic,
                json_encode([
                    'type' => 'call_ended',
                    'callId' => $call->getId(),
                    'by' => $userId,
                ]),
                true
            );
            $this->hub->publish($update);
        }

        return ['success' => true];
    }

    public function getActiveCallForUser(int $userId): ?array
    {
        $call = $this->em->getRepository(Call::class)->findOneBy([
            'status' => 'pending',
            'receiver' => $userId
        ]);

        if (!$call) {
            $call = $this->em->getRepository(Call::class)->findOneBy([
                'status' => 'accepted',
                'receiver' => $userId
            ]);
        }

        if (!$call) return null;

        return [
            'id' => $call->getId(),
            'caller_id' => $call->getCaller()->getId(),
            'callee_id' => $call->getReceiver()->getId(),
            'status' => $call->getStatus(),
            'type' => $call->getType(),
        ];
=======
    public function endCall(string $callId, int $userId): array
    {
        if (!isset($this->activeCalls[$callId])) {
            return ['success' => false, 'error' => 'Appel non trouvé'];
        }

        $call = $this->activeCalls[$callId];
        
        if ($call['caller_id'] !== $userId && $call['callee_id'] !== $userId) {
            return ['success' => false, 'error' => 'Non autorisé'];
        }

        unset($this->activeCalls[$callId]);

        return ['success' => true];
    }

    public function getCall(string $callId): ?array
    {
        return $this->activeCalls[$callId] ?? null;
    }

    public function getActiveCallForUser(int $userId): ?array
    {
        foreach ($this->activeCalls as $call) {
            if (($call['caller_id'] === $userId || $call['callee_id'] === $userId) 
                && $call['status'] !== 'ended') {
                return $call;
            }
        }
        return null;
>>>>>>> testsisi
    }
}