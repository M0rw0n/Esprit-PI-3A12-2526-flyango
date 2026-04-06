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

<<<<<<< HEAD
    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ForumPost $post = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $parentId = null;
=======
    #[ORM\Column(name: 'post_id')]
    private int $postId = 0;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: true)]
    private ?ForumPost $post = null;
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(type: 'date')]
<<<<<<< HEAD
    private $createdAt;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $score = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $likes = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $dislikes = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isPinned = false;
=======
    private \DateTimeInterface $createdAt;
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
<<<<<<< HEAD
    public function getPost(): ?ForumPost { return $this->post; }
    public function setPost(?ForumPost $p): static { $this->post = $p; return $this; }
    public function getParentId(): ?int { return $this->parentId; }
    public function setParentId(?int $id): static { $this->parentId = $id; return $this; }
=======
    public function getPostId(): int { return $this->postId; }
    public function setPostId(int $p): static { $this->postId = $p; return $this; }
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $c): static { $this->content = $c; return $this; }
<<<<<<< HEAD
    public function getCreatedAt() { return $this->createdAt; }
    public function setCreatedAt($d): static { $this->createdAt = $d; return $this; }

    public function getScore(): int { return $this->score; }
    public function setScore(int $s): static { $this->score = $s; return $this; }
    public function getLikes(): int { return $this->likes; }
    public function setLikes(int $l): static { $this->likes = $l; return $this; }
    public function getDislikes(): int { return $this->dislikes; }
    public function setDislikes(int $d): static { $this->dislikes = $d; return $this; }
    public function isIsPinned(): bool { return $this->isPinned; }
    public function setIsPinned(bool $p): static { $this->isPinned = $p; return $this; }

    public function isReply(): bool {
        return $this->parentId !== null;
    }

    public function getDepth(): int {
        return 0;
    }
=======
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
}
