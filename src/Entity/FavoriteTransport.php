<?php

namespace App\Entity;

use App\Repository\FavoriteTransportRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Entity\Trait\BlameableTrait;
=======
>>>>>>> testsisi

#[ORM\Entity(repositoryClass: FavoriteTransportRepository::class)]
#[ORM\Table(name: 'favorite_transport')]
#[ORM\UniqueConstraint(name: 'user_transport_unique', columns: ['user_id', 'transport_id'])]
class FavoriteTransport
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

    #[ORM\ManyToOne(targetEntity: TransportOffer::class)]
    #[ORM\JoinColumn(name: 'transport_id', referencedColumnName: 'transport_id', nullable: false)]
    private ?TransportOffer $transport = null;

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
    public function getTransport(): ?TransportOffer { return $this->transport; }
    public function setTransport(?TransportOffer $t): static { $this->transport = $t; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
=======
>>>>>>> testsisi
}
