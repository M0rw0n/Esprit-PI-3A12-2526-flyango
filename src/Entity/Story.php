<?php

namespace App\Entity;

use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $media = null;

    #[ORM\Column(length: 20)]
    private ?string $mediaType = 'image'; // image, video

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $caption = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: StoryView::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $views;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: StoryReaction::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $reactions;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->expiresAt = (new \DateTime())->modify('+24 hours');
        $this->views = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getMedia(): ?string { return $this->media; }
    public function setMedia(string $media): static { $this->media = $media; return $this; }
    public function getMediaType(): ?string { return $this->mediaType; }
    public function setMediaType(string $mediaType): static { $this->mediaType = $mediaType; return $this; }
    public function getCaption(): ?string { return $this->caption; }
    public function setCaption(?string $caption): static { $this->caption = $caption; return $this; }
    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): static { $this->location = $location; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getExpiresAt(): ?\DateTimeInterface { return $this->expiresAt; }
    public function getViews(): Collection { return $this->views; }
    public function getReactions(): Collection { return $this->reactions; }
    
    public function isExpired(): bool
    {
        return new \DateTime() > $this->expiresAt;
    }
}
