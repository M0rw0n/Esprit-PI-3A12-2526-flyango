<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class StoryView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Story::class, inversedBy: 'views')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Story $story = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $viewedAt = null;

    public function __construct()
    {
        $this->viewedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getStory(): ?Story { return $this->story; }
    public function setStory(?Story $story): static { $this->story = $story; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getViewedAt(): ?\DateTimeInterface { return $this->viewedAt; }
}
