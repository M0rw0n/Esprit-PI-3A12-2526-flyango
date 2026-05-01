<?php

namespace App\Entity\Passport;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'passport_history')]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PassportUser::class, inversedBy: 'history')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?PassportUser $user = null;

    #[ORM\ManyToOne(targetEntity: Puzzle::class, inversedBy: 'history')]
    #[ORM\JoinColumn(name: 'puzzle_id', nullable: false)]
    private ?Puzzle $puzzle = null;

    #[ORM\Column(name: 'completed_at', type: 'datetime')]
    private \DateTimeInterface $completedAt;

    #[ORM\Column(name: 'time_spent', type: 'integer', nullable: true)]
    private ?int $timeSpent = null;

    #[ORM\Column(name: 'points_earned', type: 'integer', options: ['default' => 0])]
    private int $pointsEarned = 0;

    public function __construct()
    {
        $this->completedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?PassportUser { return $this->user; }
    public function setUser(?PassportUser $user): static { $this->user = $user; return $this; }
    public function getPuzzle(): ?Puzzle { return $this->puzzle; }
    public function setPuzzle(?Puzzle $puzzle): static { $this->puzzle = $puzzle; return $this; }
    public function getCompletedAt(): \DateTimeInterface { return $this->completedAt; }
    public function getTimeSpent(): ?int { return $this->timeSpent; }
    public function setTimeSpent(?int $timeSpent): static { $this->timeSpent = $timeSpent; return $this; }
    public function getPointsEarned(): int { return $this->pointsEarned; }
    public function setPointsEarned(int $pointsEarned): static { $this->pointsEarned = $pointsEarned; return $this; }
}