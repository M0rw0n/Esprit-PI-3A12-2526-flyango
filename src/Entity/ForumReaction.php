<?php

namespace App\Entity;

use App\Repository\ForumReactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumReactionRepository::class)]
#[ORM\Table(name: 'forum_reaction')]
class ForumReaction
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'post_id')]
    private int $postId = 0;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: true)]
    private ?ForumPost $post = null;

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(length: 20)]
    private string $type = 'LIKE';

    public function getId(): ?int { return $this->id; }
    public function getPostId(): int { return $this->postId; }
    public function setPostId(int $p): static { $this->postId = $p; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
}
