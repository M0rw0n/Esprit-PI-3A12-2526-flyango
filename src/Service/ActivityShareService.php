<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class ActivityShareService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function shareToForum(Activity $activity, User $user): ?int
    {
        // In production, create a forum post
        // This is a placeholder that returns success
        return 1; // Return post ID
    }

    public function shareToMessenger(Activity $activity, Conversation $conversation, User $sender): Message
    {
        // Create a special message with activity preview
        $message = new Message();
        $message->setSender($sender);
        $message->setConversation($conversation);
        $message->setContent($this->formatActivityMessage($activity));
        $message->setCreatedAt(new \DateTime());
        
        // Store activity data in a way that can be rendered
        // In production, use a JSON column or separate table
        $message->setForumPostId($activity->getId()); // Reuse field to store activity ID

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    private function formatActivityMessage(Activity $activity): string
    {
        $imageUrl = $activity->getImage() ? '/uploads/' . $activity->getImage() : null;
        
        // Return formatted message for display
        return json_encode([
            'type' => 'activity',
            'activity_id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'image' => $imageUrl,
            'description' => substr($activity->getDescription() ?? '', 0, 100),
            'price' => $activity->getPrice(),
            'lieu' => $activity->getLieu(),
            'link' => '/activity/' . $activity->getId()
        ]);
    }

    public function getActivityPreviewData(Activity $activity): array
    {
        return [
            'type' => 'activity',
            'activity_id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'image' => $activity->getImage() ? '/uploads/' . $activity->getImage() : null,
            'description' => $activity->getDescription(),
            'price' => $activity->getPrice(),
            'lieu' => $activity->getLieu(),
            'category' => $activity->getCategory(),
            'duration' => $activity->getDuration(),
            'link' => '/activity/' . $activity->getId()
        ];
    }
}