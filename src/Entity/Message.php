<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'sender_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $sender = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(length: 20, options: ['default' => 'sent'])]
    private string $status = self::STATUS_SENT;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $readAt = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageReaction::class, cascade: ['remove'])]
    private Collection $reactions;

    #[ORM\ManyToOne(targetEntity: Message::class)]
    #[ORM\JoinColumn(name: 'reply_to', nullable: true)]
    private ?Message $replyTo = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $audio = null;

    #[ORM\Column(length: 50, options: ['default' => 'text'])]
    private string $type = 'text';

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = self::STATUS_SENT;
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getConversation(): ?Conversation { return $this->conversation; }
    public function setConversation(?Conversation $c): static { $this->conversation = $c; return $this; }
    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $s): static { $this->sender = $s; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $c): static { $this->content = $c; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getReadAt(): ?\DateTimeInterface { return $this->readAt; }
    public function setReadAt(?\DateTimeInterface $d): static { $this->readAt = $d; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getReactions(): Collection { return $this->reactions; }
    public function getReplyTo(): ?Message { return $this->replyTo; }
    public function setReplyTo(?Message $m): static { $this->replyTo = $m; return $this; }
    public function getAudio(): ?string { return $this->audio; }
    public function setAudio(?string $a): static { $this->audio = $a; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getMetadata(): ?array { return $this->metadata; }
    public function setMetadata(?array $m): static { $this->metadata = $m; return $this; }
}