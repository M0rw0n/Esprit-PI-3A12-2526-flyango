<?php

namespace App\Entity\Passport;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'passport_favorite')]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PassportUser::class, inversedBy: 'favorites')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?PassportUser $user = null;

    #[ORM\ManyToOne(targetEntity: Puzzle::class, inversedBy: 'favorites')]
    #[ORM\JoinColumn(name: 'puzzle_id', nullable: false)]
    private ?Puzzle $puzzle = null;

    #[ORM\Column(name: 'added_at', type: 'datetime')]
    private \DateTimeInterface $addedAt;

    public function __construct()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?PassportUser { return $this->user; }
    public function setUser(?PassportUser $user): static { $this->user = $user; return $this; }
    public function getPuzzle(): ?Puzzle { return $this->puzzle; }
    public function setPuzzle(?Puzzle $puzzle): static { $this->puzzle = $puzzle; return $this; }
    public function getAddedAt(): \DateTimeInterface { return $this->addedAt; }
<<<<<<< HEAD
=======
    public function setAddedAt(\DateTimeInterface $addedAt): static { $this->addedAt = $addedAt; return $this; }
>>>>>>> testsisi
}