<?php

namespace App\Entity;

use App\Repository\CircuitAvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CircuitAvisRepository::class)]
#[ORM\Table(name: 'circuit_avis')]
class CircuitAvis
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Circuit::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: 'id_circuit', referencedColumnName: 'id_circuit', nullable: false)]
    private ?Circuit $circuit = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'integer')]
    private int $rating = 5;

    #[ORM\Column(type: 'text')]
    private string $comment = '';

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sentimentScore = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sentimentLabel = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sentimentStars = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $sentimentConfidence = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sentimentCategory = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getCircuit(): ?Circuit { return $this->circuit; }
    public function setCircuit(?Circuit $circuit): static { $this->circuit = $circuit; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $author): static { $this->author = $author; return $this; }
    public function getRating(): int { return $this->rating; }
    public function setRating(int $rating): static { $this->rating = max(1, min(5, $rating)); return $this; }
    public function getComment(): string { return $this->comment; }
    public function setComment(string $comment): static { $this->comment = $comment; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

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

    public function getSentimentData(): array
    {
        return [
            'score' => $this->sentimentScore ?? 0,
            'label' => $this->sentimentLabel ?? 'neutral',
            'stars' => $this->sentimentStars ?? 3,
            'confidence' => $this->sentimentConfidence ?? 0,
            'category' => $this->sentimentCategory ?? 'Average'
        ];
    }

    public function setSentimentFromAnalysis(array $analysis): static
    {
        $this->sentimentScore = $analysis['score'] ?? 0;
        $this->sentimentLabel = $analysis['label'] ?? 'neutral';
        $this->sentimentStars = $analysis['stars'] ?? 3;
        $this->sentimentConfidence = $analysis['confidence'] ?? 0;
        $this->sentimentCategory = $analysis['category'] ?? 'Average';
        return $this;
    }
}
