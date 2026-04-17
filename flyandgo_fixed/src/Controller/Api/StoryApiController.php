<?php

namespace App\Controller\Api;

use App\Entity\Story;
use App\Entity\User;
use App\Repository\StoryRepository;
use App\Service\StoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/story')]
class StoryApiController extends AbstractController
{
    public function __construct(
        private StoryService $storyService,
        private StoryRepository $storyRepo,
        private EntityManagerInterface $em
    ) {}

    #[Route('/create', name: 'api_story_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
            }

            // Refresh user to avoid session mismatches
            $user = $this->em->getRepository(User::class)->find($user->getId());

            $file = $request->files->get('media');
            if (!$file) {
                return new JsonResponse(['success' => false, 'error' => 'Média manquant'], 400);
            }

            $caption = $request->request->get('caption');
            $location = $request->request->get('location');

            $story = $this->storyService->createStory($user, $file, $caption, $location);
            return new JsonResponse([
                'success' => true,
                'story' => [
                    'id' => $story->getId(),
                    'media' => $story->getMedia(),
                    'type' => $story->getMediaType()
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    #[Route('/feed', name: 'api_story_feed', methods: ['GET'])]
    public function feed(): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
            }

            // Refresh user to avoid session mismatches
            $user = $this->em->getRepository(User::class)->find($user->getId());

            $stories = $this->storyRepo->findActiveFeed($user);
            
            // Group by user
            $grouped = [];
            foreach ($stories as $story) {
                $author = $story->getUser();
                $authorId = $author->getId();
                
                if (!isset($grouped[$authorId])) {
                    $grouped[$authorId] = [
                        'userId' => $authorId,
                        'userName' => $author->getFullName(),
                        'userAvatar' => $author->getAvatar(),
                        'stories' => []
                    ];
                }
                
                $grouped[$authorId]['stories'][] = [
                    'id' => $story->getId(),
                    'media' => $story->getMedia(),
                    'type' => $story->getMediaType(),
                    'caption' => $story->getCaption(),
                    'location' => $story->getLocation(),
                    'createdAt' => $story->getCreatedAt()->format('c'),
                    'isOwn' => $author->getId() === $user->getId(),
                    'viewed' => $story->getViews()->exists(fn($i, $v) => $v->getUser() && $v->getUser()->getId() === $user->getId())
                ];
            }

            return new JsonResponse([
                'success' => true,
                'feed' => array_values($grouped)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    #[Route('/{id}/view', name: 'api_story_view', methods: ['POST'])]
    public function view(Story $story): JsonResponse
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            $this->storyService->addView($story, $user);
        }
        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/react', name: 'api_story_react', methods: ['POST'])]
    public function react(Story $story, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $emoji = $data['emoji'] ?? '❤️';

        $this->storyService->addReaction($story, $user, $emoji);
        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/reply', name: 'api_story_reply', methods: ['POST'])]
    public function reply(Story $story, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        if (empty($text)) {
            return new JsonResponse(['success' => false, 'error' => 'Texte vide'], 400);
        }

        $this->storyService->replyToStory($story, $user, $text);
        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}', name: 'api_story_delete', methods: ['DELETE'])]
    public function delete(Story $story): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User || $story->getUser() !== $user) {
            return new JsonResponse(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $this->em->remove($story);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }
}
