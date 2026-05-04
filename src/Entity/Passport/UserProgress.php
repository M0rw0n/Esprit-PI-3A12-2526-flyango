<?php

namespace App\Entity\Passport;

use App\Repository\Passport\UserProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProgressRepository::class)]
#[ORM\Table(name: 'passport_user_progress')]
class UserProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PassportUser::class, inversedBy: 'progress')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?PassportUser $user = null;

    #[ORM\ManyToOne(targetEntity: Puzzle::class, inversedBy: 'userProgress')]
    #[ORM\JoinColumn(name: 'puzzle_id', nullable: false)]
    private ?Puzzle $puzzle = null;

    #[ORM\Column(name: 'is_completed', type: 'boolean', options: ['default' => false])]
    private bool $isCompleted = false;

    #[ORM\Column(name: 'completion_percentage', type: 'integer', options: ['default' => 0])]
    private int $completionPercentage = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $score = 0;

    #[ORM\Column(name: 'completed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(name: 'time_spent', type: 'integer', nullable: true)]
    private ?int $timeSpent = null;

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?PassportUser { return $this->user; }
    public function setUser(?PassportUser $user): static { $this->user = $user; return $this; }
    public function getPuzzle(): ?Puzzle { return $this->puzzle; }
    public function setPuzzle(?Puzzle $puzzle): static { $this->puzzle = $puzzle; return $this; }
    public function isCompleted(): bool { return $this->isCompleted; }
    public function setCompleted(bool $completed): static { $this->isCompleted = $completed; return $this; }
    public function getCompletionPercentage(): int { return $this->completionPercentage; }
    public function setCompletionPercentage(int $percentage): static { $this->completionPercentage = $percentage; return $this; }
    public function getScore(): int { return $this->score; }
    public function setScore(int $score): static { $this->score = $score; return $this; }
    public function getCompletedAt(): ?\DateTimeInterface { return $this->completedAt; }
<<<<<<< HEAD
=======
    public function setCompletedAt(?\DateTimeInterface $completedAt): static { $this->completedAt = $completedAt; return $this; }
>>>>>>> testsisi
    public function getTimeSpent(): ?int { return $this->timeSpent; }
    public function setTimeSpent(?int $timeSpent): static { $this->timeSpent = $timeSpent; return $this; }
}