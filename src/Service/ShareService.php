<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\Circuit;
use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\ForumPost;
use Doctrine\ORM\EntityManagerInterface;

class ShareService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function shareActivityToMessenger(Activity $activity, Conversation $conversation, User $sender): Message
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setConversation($conversation);
        $message->setContent('A partagé une activité : ' . $activity->getTitle());
        $message->setType('SHARE_ACTIVITY');
        $message->setCreatedAt(new \DateTime());
        
        $message->setMetadata([
            'id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'image' => $activity->getImage(),
            'price' => $activity->getPrice(),
            'location' => $activity->getLieu(),
            'category' => $activity->getCategory()
        ]);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function shareCircuitToMessenger(Circuit $circuit, Conversation $conversation, User $sender): Message
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setConversation($conversation);
        $message->setContent('A partagé un circuit : ' . $circuit->getTitre());
        $message->setType('SHARE_CIRCUIT');
        $message->setCreatedAt(new \DateTime());
        
        $message->setMetadata([
            'id' => $circuit->getId(),
            'title' => $circuit->getTitre(),
            'image' => $circuit->getImage(),
            'price' => $circuit->getPrix(),
            'location' => $circuit->getDestination(),
            'duration' => $circuit->getDuree()
        ]);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function shareActivityToForum(Activity $activity, User $user): ForumPost
    {
        $post = new ForumPost();
        $post->setTitle('Découvrez cette activité : ' . $activity->getTitle());
        $post->setContent("Je vous recommande vivement cette activité : " . $activity->getTitle() . ". \n\n" . 
                         "Lieu : " . ($activity->getLieu() ?? 'Tunisie') . "\n" .
                         "Prix : " . $activity->getPrice() . " TND\n\n" .
                         "Découvrez plus de détails ici !");
        $post->setAuthor($user->getFullName());
        $post->setAuthorId($user->getId());
        $post->setCategorie('Activités');
        $post->setImage($activity->getImage());
        $post->setStatus('APPROVED');
        $post->setCreatedAt(new \DateTime());

        $this->em->persist($post);
        $this->em->flush();

        return $post;
    }

    public function shareCircuitToForum(Circuit $circuit, User $user): ForumPost
    {
        $post = new ForumPost();
        $post->setTitle('Super circuit à découvrir : ' . $circuit->getTitre());
        $post->setContent("Regardez ce circuit magnifique : " . $circuit->getTitre() . ". \n\n" . 
                         "Destination : " . $circuit->getDestination() . "\n" .
                         "Prix : " . $circuit->getPrix() . " TND\n" .
                         "Durée : " . $circuit->getDuree() . "\n\n" .
                         "Tous les détails sur Fly&Go !");
        $post->setAuthor($user->getFullName());
        $post->setAuthorId($user->getId());
        $post->setCategorie('Circuits');
        $post->setImage($circuit->getImage());
        $post->setStatus('APPROVED');
        $post->setCreatedAt(new \DateTime());

        $this->em->persist($post);
        $this->em->flush();

        return $post;
    }
}
