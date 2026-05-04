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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $authorId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column(type: 'date')]
    private $createdAt;

    #[ORM\Column(length: 20)]
    private string $status = 'APPROVED';

    #[ORM\Column(type: 'integer')]
    private int $vues = 0;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $image = null;

<<<<<<< HEAD
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: ForumComment::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
=======
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: ForumComment::class, cascade: ['remove'])]
>>>>>>> testsisi
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $t): static { $this->title = $t; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $c): static { $this->content = $c; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getAuthorId(): ?int { return $this->authorId; }
    public function setAuthorId(?int $id): static { $this->authorId = $id; return $this; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $c): static { $this->categorie = $c; return $this; }
    public function getCreatedAt() { return $this->createdAt; }
<<<<<<< HEAD
=======
    public function setCreatedAt($d): static { $this->createdAt = $d; return $this; }
>>>>>>> testsisi
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }
    public function getVues(): int { return $this->vues; }
    public function setVues(int $v): static { $this->vues = $v; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getComments(): Collection { return $this->comments; }
}
