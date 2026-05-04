<?php

namespace App\Entity;

use App\Repository\TransportAvisRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Entity\Trait\BlameableTrait;
=======
>>>>>>> testsisi

#[ORM\Entity(repositoryClass: TransportAvisRepository::class)]
#[ORM\Table(name: 'avis_transport')]
class TransportAvis
{
<<<<<<< HEAD
    use BlameableTrait;

=======
>>>>>>> testsisi
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_avis_transport')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TransportOffer::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'transport_id')]
    private ?TransportOffer $transportOffer = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private string $author = '';

    #[ORM\Column(type: 'integer')]
    private int $rating = 5;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
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
    public function getTransportOffer(): ?TransportOffer { return $this->transportOffer; }
    public function setTransportOffer(?TransportOffer $o): static { $this->transportOffer = $o; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $a): static { $this->author = $a; return $this; }
    public function getRating(): int { return $this->rating; }
    public function setRating(int $r): static { $this->rating = max(1, min(5, $r)); return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $c): static { $this->comment = $c; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
=======
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
>>>>>>> testsisi
}
