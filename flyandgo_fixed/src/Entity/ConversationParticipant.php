<?php

namespace App\Entity;

use App\Repository\ConversationParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationParticipantRepository::class)]
#[ORM\Table(name: 'conversation_participant')]
class ConversationParticipant
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'conversation_id', nullable: false)]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $unreadCount = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastReadAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $joinedAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isMuted = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isArchived = false;

    public function __construct()
    {
        $this->joinedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getConversation(): ?Conversation { return $this->conversation; }
    public function setConversation(?Conversation $c): static { $this->conversation = $c; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getUnreadCount(): int { return $this->unreadCount; }
    public function setUnreadCount(int $c): static { $this->unreadCount = $c; return $this; }
    public function getLastReadAt(): ?\DateTimeInterface { return $this->lastReadAt; }
    public function setLastReadAt(?\DateTimeInterface $d): static { $this->lastReadAt = $d; return $this; }
    public function getJoinedAt(): \DateTimeInterface { return $this->joinedAt; }
    public function setJoinedAt(\DateTimeInterface $d): static { $this->joinedAt = $d; return $this; }
    public function isMuted(): bool { return $this->isMuted; }
    public function setIsMuted(bool $v): static { $this->isMuted = $v; return $this; }
    public function isArchived(): bool { return $this->isArchived; }
    public function setIsArchived(bool $v): static { $this->isArchived = $v; return $this; }
    public function incrementUnread(): static { $this->unreadCount++; return $this; }
    public function resetUnread(): static { $this->unreadCount = 0; return $this; }
}