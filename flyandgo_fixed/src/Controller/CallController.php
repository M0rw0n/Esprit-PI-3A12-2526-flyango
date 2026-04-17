<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CallService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api/calls')]
class CallController extends AbstractController
{
    public function __construct(
        private CallService $callService,
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
    ) {}

    private function getCurrentUser(): ?User
    {
        try {
            $token = $this->tokenStorage->getToken();
            $user = $token?->getUser();
            return $user instanceof User ? $user : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    #[Route('/initiate', name: 'api_calls_initiate', methods: ['POST'])]
    public function initiateCall(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $calleeId = (int) $request->request->get('callee_id');
            $conversationId = (int) $request->request->get('conversation_id');
            $type = $request->request->get('type', 'video');

            if (!$calleeId || !$conversationId) return new JsonResponse(['error' => 'Missing parameters'], 400);

            $result = $this->callService->initiateCall($user->getId(), $calleeId, $conversationId, $type);
            return new JsonResponse(['success' => true, 'call' => $result]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/accept', name: 'api_calls_accept', methods: ['POST'])]
    public function acceptCall(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $callId = (int) $request->request->get('call_id');
            return new JsonResponse($this->callService->acceptCall($callId, $user->getId()));
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/reject', name: 'api_calls_reject', methods: ['POST'])]
    public function rejectCall(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $callId = (int) $request->request->get('call_id');
            return new JsonResponse($this->callService->rejectCall($callId, $user->getId()));
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/end', name: 'api_calls_end', methods: ['POST'])]
    public function endCall(Request $request): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $callId = (int) $request->request->get('call_id');
            return new JsonResponse($this->callService->endCall($callId, $user->getId()));
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/status', name: 'api_calls_status', methods: ['GET'])]
    public function getCallStatus(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return new JsonResponse(['error' => 'Unauthorized'], 401);

            $activeCall = $this->callService->getActiveCallForUser($user->getId());
            return new JsonResponse(['hasActiveCall' => $activeCall !== null, 'call' => $activeCall]);
        } catch (\Throwable $e) {
            return new JsonResponse(['hasActiveCall' => false]);
        }
    }
}
