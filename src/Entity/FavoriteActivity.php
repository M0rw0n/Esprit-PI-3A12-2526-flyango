<?php

namespace App\Entity;

use App\Repository\FavoriteActivityRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Entity\Trait\BlameableTrait;
=======
>>>>>>> testsisi

#[ORM\Entity(repositoryClass: FavoriteActivityRepository::class)]
#[ORM\Table(name: 'favorite_activity')]
#[ORM\UniqueConstraint(name: 'user_activity_unique', columns: ['user_id', 'activity_id'])]
class FavoriteActivity
{
<<<<<<< HEAD
    use BlameableTrait;
=======
>>>>>>> testsisi
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    #[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'id', nullable: false)]
    private ?Activity $activity = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

<<<<<<< HEAD
    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

=======
>>>>>>> testsisi
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getActivity(): ?Activity { return $this->activity; }
    public function setActivity(?Activity $a): static { $this->activity = $a; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
=======
>>>>>>> testsisi
}
