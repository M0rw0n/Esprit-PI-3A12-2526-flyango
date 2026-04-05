<?php

namespace App\Entity;

use App\Repository\ForumPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumPostRepository::class)]
#[ORM\Table(name: 'forum_post')]
class ForumPost
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(length: 20)]
    private string $status = 'APPROVED';

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: ForumComment::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: ForumReaction::class, cascade: ['remove'])]
    private Collection $reactions;

    public function __construct()
    {
        $this->comments  = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $t): static { $this->title = $t; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $c): static { $this->content = $c; return $this; }

    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }

    public function getComments(): Collection { return $this->comments; }
    public function getReactions(): Collection { return $this->reactions; }

    public function addComment(ForumComment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
        return $this;
    }
}
