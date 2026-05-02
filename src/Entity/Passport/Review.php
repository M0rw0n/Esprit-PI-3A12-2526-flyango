<?php

namespace App\Entity\Passport;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'passport_review')]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PassportUser::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?PassportUser $user = null;

    #[ORM\ManyToOne(targetEntity: Puzzle::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: 'puzzle_id', nullable: false)]
    private ?Puzzle $puzzle = null;

    #[ORM\Column(type: 'integer')]
    private int $rating = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?PassportUser { return $this->user; }
    public function setUser(?PassportUser $user): static { $this->user = $user; return $this; }
    public function getPuzzle(): ?Puzzle { return $this->puzzle; }
    public function setPuzzle(?Puzzle $puzzle): static { $this->puzzle = $puzzle; return $this; }
    public function getRating(): int { return $this->rating; }
    public function setRating(int $rating): static { $this->rating = $rating; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): static { $this->comment = $comment; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}