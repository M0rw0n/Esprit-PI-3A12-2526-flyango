<?php

namespace App\Entity;

use App\Repository\CallRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallRepository::class)]
#[ORM\Table(name: 'app_call')]
#[ORM\Index(columns: ['caller_id'], name: 'idx_call_caller')]
#[ORM\Index(columns: ['receiver_id'], name: 'idx_call_receiver')]
#[ORM\Index(columns: ['conversation_id'], name: 'idx_call_conversation')]
class Call
{
    const STATUS_MISSED = 'missed';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_ANSWER = 'no_answer';
    
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'caller_id', nullable: false)]
    private ?User $caller = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'receiver_id', nullable: false)]
    private ?User $receiver = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(name: 'conversation_id', nullable: true)]
    private ?Conversation $conversation = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_MISSED;

    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_AUDIO;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    
    public function getCaller(): ?User { return $this->caller; }
    public function setCaller(?User $caller): static { $this->caller = $caller; return $this; }
    
    public function getReceiver(): ?User { return $this->receiver; }
    public function setReceiver(?User $receiver): static { $this->receiver = $receiver; return $this; }
    
    public function getConversation(): ?Conversation { return $this->conversation; }
    public function setConversation(?Conversation $conversation): static { $this->conversation = $conversation; return $this; }
    
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    
    public function getType(): string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }
    
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    
    public function getDuration(): ?int { return $this->duration; }
    public function setDuration(?int $duration): static { $this->duration = $duration; return $this; }
    
    public function getStartedAt(): ?\DateTimeInterface { return $this->startedAt; }
    public function getEndedAt(): ?\DateTimeInterface { return $this->endedAt; }
    
    public function accept(): void
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->startedAt = new \DateTime();
    }
    
    public function end(): void
    {
        $this->endedAt = new \DateTime();
        if ($this->startedAt) {
            $this->duration = $this->endedAt->getTimestamp() - $this->startedAt->getTimestamp();
        }
    }
    
    public function decline(): void
    {
        $this->status = self::STATUS_DECLINED;
    }
    
    public function miss(): void
    {
        $this->status = self::STATUS_MISSED;
    }
    
    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
    }
    
    public function getDurationFormatted(): string
    {
        if (!$this->duration) return '0:00';
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes % 60;
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
