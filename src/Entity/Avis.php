<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: 'avis_hebergement')]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_avis_hebergement', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hebergement::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: 'id_hebergement', referencedColumnName: 'id_hebergement', nullable: true)]
    private ?Hebergement $hebergement = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'La note doit être entre {{ min }} et {{ max }}')]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $note = null;

    #[Assert\Length(min: 10, max: 2000, minMessage: 'Min 10 caractères', maxMessage: 'Max 2000 caractères')]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

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

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $sentimentSource = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getHebergement(): ?Hebergement { return $this->hebergement; }
    public function setHebergement(?Hebergement $h): static { $this->hebergement = $h; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getNote(): ?int { return $this->note; }
    public function setNote(?int $n): static { $this->note = max(1, min(5, $n ?? 5)); return $this; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $c): static { $this->commentaire = $c; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getAuteur(): string { return $this->user ? $this->user->getFullName() : 'Anonyme'; }
    public function setAuteur(string $a): static { return $this; }

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
    public function getSentimentSource(): ?string { return $this->sentimentSource; }
    public function setSentimentSource(?string $s): static { $this->sentimentSource = $s; return $this; }

    public function getSentimentData(): array
    {
        return [
            'score' => $this->sentimentScore ?? 0,
            'label' => $this->sentimentLabel ?? 'neutral',
            'stars' => $this->sentimentStars ?? 3,
            'confidence' => $this->sentimentConfidence ?? 0,
            'category' => $this->sentimentCategory ?? 'Average',
            'source' => $this->sentimentSource ?? 'none'
        ];
    }

    public function setSentimentFromAnalysis(array $analysis): static
    {
        $this->sentimentScore = $analysis['score'] ?? 0;
        $this->sentimentLabel = $analysis['label'] ?? 'neutral';
        $this->sentimentStars = $analysis['stars'] ?? 3;
        $this->sentimentConfidence = $analysis['confidence'] ?? 0;
        $this->sentimentCategory = $analysis['category'] ?? 'Average';
        $this->sentimentSource = $analysis['source'] ?? 'local';
        return $this;
    }
}
