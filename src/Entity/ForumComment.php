<?php

namespace App\Entity;

use App\Repository\ForumCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumCommentRepository::class)]
#[ORM\Table(name: 'forum_comment')]
class ForumComment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'post_id')]
    private int $postId = 0;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: true)]
    private ?ForumPost $post = null;

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getPostId(): int { return $this->postId; }
    public function setPostId(int $p): static { $this->postId = $p; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $c): static { $this->content = $c; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
}
