<?php

namespace App\Entity;

use App\Repository\BlockedUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockedUserRepository::class)]
#[ORM\Table(name: 'blocked_user')]
class BlockedUser
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'blocked_user_id', nullable: false)]
    private ?User $blockedUser = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getBlockedUser(): ?User { return $this->blockedUser; }
    public function setBlockedUser(?User $blockedUser): self { $this->blockedUser = $blockedUser; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
