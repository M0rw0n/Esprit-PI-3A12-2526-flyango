<?php

namespace App\Entity;

use App\Repository\MessageReactionRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Entity\Trait\BlameableTrait;
=======
>>>>>>> testsisi

#[ORM\Entity(repositoryClass: MessageReactionRepository::class)]
#[ORM\Table(name: 'message_reaction')]
class MessageReaction
{
<<<<<<< HEAD
    use BlameableTrait;
=======
>>>>>>> testsisi
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'reactions')]
<<<<<<< HEAD
    #[ORM\JoinColumn(name: 'message_id', nullable: false, onDelete: 'CASCADE')]
=======
    #[ORM\JoinColumn(name: 'message_id', nullable: false)]
>>>>>>> testsisi
    private ?Message $message = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 10)]
    private string $emoji = '';

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

<<<<<<< HEAD
    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

=======
>>>>>>> testsisi
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getMessage(): ?Message { return $this->message; }
    public function setMessage(?Message $m): static { $this->message = $m; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getEmoji(): string { return $this->emoji; }
    public function setEmoji(string $e): static { $this->emoji = $e; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
}
=======
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
}
>>>>>>> testsisi
