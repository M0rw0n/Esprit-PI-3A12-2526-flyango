<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'review')]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: 'activity_id')]
    private ?Activity $activity = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'integer')]
    private int $rating = 5;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'created_at', type: 'date')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sentimentScore = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sentimentLabel = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sentimentStars = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sentimentConfidence = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sentimentCategory = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getActivity(): ?Activity { return $this->activity; }
    public function setActivity(?Activity $a): static { $this->activity = $a; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getRating(): int { return $this->rating; }
    public function setRating(int $r): static { $this->rating = max(1, min(5, $r)); return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $c): static { $this->comment = $c; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getSentimentScore(): ?float { return $this->sentimentScore; }
    public function setSentimentScore(?float $s): static { $this->sentimentScore = $s; return $this; }
    public function getSentimentLabel(): ?string { return $this->sentimentLabel; }
    public function setSentimentLabel(?string $l): static { $this->sentimentLabel = $l; return $this; }
    public function getSentimentStars(): ?int { return $this->sentimentStars; }
    public function setSentimentStars(?int $s): static { $this->sentimentStars = $s; return $this; }
    public function getSentimentConfidence(): ?float { return $this->sentimentConfidence; }
    public function setSentimentConfidence(?float $c): static { $this->sentimentConfidence = $c; return $this; }
    public function getSentimentCategory(): ?string { return $this->sentimentCategory; }
    public function setSentimentCategory(?string $c): static { $this->sentimentCategory = $c; return $this; }

    public function setSentimentFromAnalysis(array $analysis): static
    {
        $this->sentimentScore = $analysis['score'] ?? null;
        $this->sentimentLabel = $analysis['label'] ?? null;
        $this->sentimentStars = $analysis['stars'] ?? null;
        $this->sentimentConfidence = $analysis['confidence'] ?? null;
        $this->sentimentCategory = $analysis['category'] ?? null;
        return $this;
    }
}
