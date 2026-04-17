<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Story;
use App\Entity\StoryReaction;
use App\Entity\StoryView;
use App\Entity\User;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class StoryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private StoryRepository $storyRepo,
        private MessageService $messageService,
        private ?HubInterface $hub = null,
        private string $projectDir = ''
    ) {
        if (!$this->projectDir) {
            $this->projectDir = dirname(__DIR__, 2);
        }
    }

    public function createStory(User $user, UploadedFile $file, ?string $caption = null, ?string $location = null): Story
    {
        $uploadPath = $this->projectDir . '/public/uploads/stories';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $mimeType = $file->getMimeType() ?? '';
        $mediaType = str_contains($mimeType, 'video') ? 'video' : 'image';
        
        $fileName = uniqid() . '.' . $file->guessExtension();
        $file->move($uploadPath, $fileName);

        $story = new Story();
        $story->setUser($user);
        $story->setMedia('/uploads/stories/' . $fileName);
        $story->setMediaType($mediaType);
        $story->setCaption($caption);
        $story->setLocation($location);

        $this->em->persist($story);
        $this->em->flush();

        try {
            $this->publishMercureUpdate('new_story', [
                'id' => $story->getId(),
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getFullName(),
                    'avatar' => $user->getAvatar()
                ]
            ]);
        } catch (\Exception $e) {
            // Mercure error shouldn't block story creation
        }

        return $story;
    }

    public function addView(Story $story, User $user): void
    {
        // Don't count own view
        if ($story->getUser() === $user) return;

        // Check if already viewed
        $existing = $this->em->getRepository(StoryView::class)->findOneBy([
            'story' => $story,
            'user' => $user
        ]);

        if (!$existing) {
            $view = new StoryView();
            $view->setStory($story);
            $view->setUser($user);
            $this->em->persist($view);
            $this->em->flush();

            $this->publishMercureUpdate('story_view', [
                'storyId' => $story->getId(),
                'viewerId' => $user->getId()
            ], 'story/' . $story->getId());
        }
    }

    public function addReaction(Story $story, User $user, string $emoji): StoryReaction
    {
        $reaction = new StoryReaction();
        $reaction->setStory($story);
        $reaction->setUser($user);
        $reaction->setEmoji($emoji);

        $this->em->persist($reaction);
        $this->em->flush();

        $this->publishMercureUpdate('story_reaction', [
            'storyId' => $story->getId(),
            'emoji' => $emoji,
            'user' => $user->getFullName()
        ], 'story/' . $story->getId());

        return $reaction;
    }

    public function replyToStory(Story $story, User $sender, string $text): Message
    {
        $conversation = $this->messageService->getOrCreatePrivateConversation($sender, $story->getUser());
        
        $message = new Message();
        $message->setConversation($conversation);
        $message->setSender($sender);
        $message->setContent($text);
        $message->setType('STORY_REPLY');
        $message->setMetadata([
            'storyId' => $story->getId(),
            'storyMedia' => $story->getMedia(),
            'storyAuthor' => $story->getUser()->getFullName(),
            'text' => $text
        ]);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function deleteExpiredStories(): int
    {
        return $this->storyRepo->deleteExpired();
    }

    private function publishMercureUpdate(string $type, array $data, string $topic = 'stories'): void
    {
        if (!$this->hub) return;

        $update = new Update(
            $topic,
            json_encode(['type' => $type, 'data' => $data])
        );

        $this->hub->publish($update);
    }
}
