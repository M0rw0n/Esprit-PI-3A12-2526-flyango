<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class StoryReaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Story::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Story $story = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 20)]
    private ?string $emoji = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $reactedAt = null;

    public function __construct()
    {
        $this->reactedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getStory(): ?Story { return $this->story; }
    public function setStory(?Story $story): static { $this->story = $story; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getEmoji(): ?string { return $this->emoji; }
    public function setEmoji(string $emoji): static { $this->emoji = $emoji; return $this; }
    public function getReactedAt(): ?\DateTimeInterface { return $this->reactedAt; }
}
