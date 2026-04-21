<?php

namespace App\Entity;

use App\Repository\LikeDislikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeDislikeRepository::class)]
#[ORM\Table(name: 'like_dislike')]
#[ORM\UniqueConstraint(name: 'user_target_unique', columns: ['user_id', 'target_type', 'target_id'])]
class LikeDislike
{
    public const TYPE_POST = 'post';
    public const TYPE_ACTIVITY = 'activity';
    public const TYPE_COMMENT = 'comment';
    public const LIKE = 1;
    public const DISLIKE = -1;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(name: 'target_type', length: 20)]
    private string $targetType = '';

    #[ORM\Column(name: 'target_id', type: 'integer')]
    private int $targetId = 0;

    #[ORM\Column(type: 'integer')]
    private int $vote = 0;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getTargetType(): string { return $this->targetType; }
    public function setTargetType(string $t): static { $this->targetType = $t; return $this; }
    public function getTargetId(): int { return $this->targetId; }
    public function setTargetId(int $id): static { $this->targetId = $id; return $this; }
    public function getVote(): int { return $this->vote; }
    public function setVote(int $v): static { $this->vote = $v; return $this; }
    public function isLike(): bool { return $this->vote === self::LIKE; }
    public function isDislike(): bool { return $this->vote === self::DISLIKE; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
