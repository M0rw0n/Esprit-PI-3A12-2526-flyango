<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api/groups')]
class GroupController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokenStorage,
    ) {}

    private function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        return $token?->getUser() instanceof User ? $token->getUser() : null;
    }

    #[Route('/create', name: 'api_groups_create', methods: ['POST'])]
    public function createGroup(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $name = $request->request->get('name', '');
        if (!$name) {
            return new JsonResponse(['error' => 'Group name required'], 400);
        }

        $userIds = $request->request->get('members', []);
        if (!is_array($userIds)) {
            $userIds = array_filter(explode(',', $userIds));
        }

        $members = [];
        foreach ($userIds as $id) {
            $member = $this->em->getRepository(User::class)->find((int)$id);
            if ($member) {
                $members[] = $member;
            }
        }

        $conversation = $this->messageService->createGroupConversation($name, $user);

        foreach ($members as $member) {
            if ($member->getId() !== $user->getId()) {
                $this->messageService->addParticipant($conversation, $member);
            }
        }

        return new JsonResponse([
            'success' => true,
            'conversation' => $this->messageService->getConversationForApi($conversation, $user),
        ]);
    }

    #[Route('/{id}/add', name: 'api_groups_add_member', methods: ['POST'])]
    public function addMember(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($id);
        if (!$conversation || $conversation->getType() !== Conversation::TYPE_GROUP) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        if ($conversation->getCreatedBy()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Only admin can add members'], 403);
        }

        $userId = $request->request->get('userId');
        $newMember = $this->em->getRepository(User::class)->find($userId);
        if (!$newMember) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->messageService->addParticipant($conversation, $newMember);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/remove', name: 'api_groups_remove_member', methods: ['POST'])]
    public function removeMember(int $id, Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($id);
        if (!$conversation || $conversation->getType() !== Conversation::TYPE_GROUP) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        if ($conversation->getCreatedBy()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Only admin can remove members'], 403);
        }

        $userId = $request->request->get('userId');
        $member = $this->em->getRepository(User::class)->find($userId);
        if (!$member) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->messageService->removeParticipant($conversation, $member);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/members', name: 'api_groups_members', methods: ['GET'])]
    public function getMembers(int $id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $conversation = $this->em->getRepository(Conversation::class)->find($id);
        if (!$conversation) {
            return new JsonResponse(['error' => 'Conversation not found'], 404);
        }

        $isParticipant = false;
        foreach ($conversation->getParticipants() as $p) {
            if ($p->getUser()->getId() === $user->getId()) {
                $isParticipant = true;
                break;
            }
        }

        if (!$isParticipant) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        $members = [];
        foreach ($conversation->getParticipants() as $p) {
            $u = $p->getUser();
            $members[] = [
                'id' => $u->getId(),
                'name' => $u->getPrenom() . ' ' . $u->getNom(),
                'avatar' => $u->getAvatar(),
                'isAdmin' => $conversation->getCreatedBy()->getId() === $u->getId(),
            ];
        }

        return new JsonResponse(['members' => $members]);
    }
}