<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CallService
{
    private array $activeCalls = [];
    private array $pendingCalls = [];

    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function initiateCall(int $callerId, int $calleeId, int $conversationId, string $type = 'video'): array
    {
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
            'callerId' => $callerId,
            'calleeId' => $calleeId,
            'type' => $type,
            'conversationId' => $conversationId,
        ];
    }

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

        return ['success' => true];
    }

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
    }
}