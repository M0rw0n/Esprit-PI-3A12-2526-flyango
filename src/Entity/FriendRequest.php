<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
#[ORM\Table(name: 'friend_request')]
#[ORM\Index(columns: ['sender_id'], name: 'idx_friend_sender')]
#[ORM\Index(columns: ['receiver_id'], name: 'idx_friend_receiver')]
#[ORM\Index(columns: ['status'], name: 'idx_friend_status')]
class FriendRequest
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'sender_id', nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'receiver_id', nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $respondedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = self::STATUS_PENDING;
    }

    public function getId(): ?int { return $this->id; }
    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $s): static { $this->sender = $s; return $this; }
    public function getReceiver(): ?User { return $this->receiver; }
    public function setReceiver(?User $r): static { $this->receiver = $r; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getRespondedAt(): ?\DateTimeInterface { return $this->respondedAt; }
    public function setRespondedAt(?\DateTimeInterface $d): static { $this->respondedAt = $d; return $this; }

    public function accept(): void
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->respondedAt = new \DateTime();
    }

    public function reject(): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->respondedAt = new \DateTime();
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
