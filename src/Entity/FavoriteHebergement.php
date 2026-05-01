<?php

namespace App\Entity;

use App\Repository\FavoriteHebergementRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\BlameableTrait;

#[ORM\Entity(repositoryClass: FavoriteHebergementRepository::class)]
#[ORM\Table(name: 'favorite_hebergement')]
#[ORM\UniqueConstraint(name: 'user_hebergement_unique', columns: ['user_id', 'hebergement_id'])]
class FavoriteHebergement
{
    use BlameableTrait;
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Hebergement::class)]
    #[ORM\JoinColumn(name: 'hebergement_id', referencedColumnName: 'id_hebergement', nullable: false)]
    private ?Hebergement $hebergement = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getHebergement(): ?Hebergement { return $this->hebergement; }
    public function setHebergement(?Hebergement $h): static { $this->hebergement = $h; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
}
