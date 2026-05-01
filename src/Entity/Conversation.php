<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Message;
use App\Entity\ConversationParticipant;
use App\Entity\Trait\BlameableTrait;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(name: 'conversation')]
class Conversation
{
    use BlameableTrait;
    const TYPE_PRIVATE = 'private';
    const TYPE_GROUP = 'group';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $type = self::TYPE_PRIVATE;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: ConversationParticipant::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $participants;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getName(): ?string { return $this->name; }
    public function setName(?string $n): static { $this->name = $n; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getMessages(): Collection { return $this->messages; }
    public function getParticipants(): Collection { return $this->participants; }

    public function getOtherUser(User $user): ?User
    {
        foreach ($this->participants as $participant) {
            if ($participant->getUser() && $participant->getUser()->getId() !== $user->getId()) {
                return $participant->getUser();
            }
        }
        return null;
    }
}
