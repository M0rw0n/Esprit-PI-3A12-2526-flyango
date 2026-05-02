<?php

namespace App\Entity;

use App\Repository\FavoriteCircuitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteCircuitRepository::class)]
#[ORM\Table(name: 'favorite_circuit')]
#[ORM\UniqueConstraint(name: 'user_circuit_unique', columns: ['user_id', 'circuit_id'])]
class FavoriteCircuit
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Circuit::class)]
    #[ORM\JoinColumn(name: 'circuit_id', referencedColumnName: 'id_circuit', nullable: false)]
    private ?Circuit $circuit = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getCircuit(): ?Circuit { return $this->circuit; }
    public function setCircuit(?Circuit $c): static { $this->circuit = $c; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
