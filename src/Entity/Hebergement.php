<?php

namespace App\Entity;

use App\Repository\HebergementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HebergementRepository::class)]
#[ORM\Table(name: 'hebergement')]
class Hebergement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_hebergement', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(length: 100)]
    private string $nom = '';

    #[ORM\Column(length: 50)]
    private string $ville = '';

    #[ORM\Column(length: 50)]
    private string $type = '';

    #[ORM\Column(name: 'prix_par_nuit', type: 'float')]
    private float $prixParNuit = 0;

    #[ORM\Column(name: 'image_path', length: 500, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'nombre_chambres', type: 'integer', nullable: true)]
    private ?int $capacite = null;

    #[ORM\Column(type: 'boolean')]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'hebergement', targetEntity: Reservation::class, cascade: ['remove'])]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'hebergement', targetEntity: Avis::class, cascade: ['remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $id): static { $this->userId = $id; return $this; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $n): static { $this->nom = $n; return $this; }
    public function getVille(): string { return $this->ville; }
    public function setVille(string $v): static { $this->ville = $v; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getPrixParNuit(): float { return $this->prixParNuit; }
    public function setPrixParNuit(float $p): static { $this->prixParNuit = $p; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $a): static { $this->adresse = $a; return $this; }
    public function getCapacite(): ?int { return $this->capacite; }
    public function setCapacite(?int $c): static { $this->capacite = $c; return $this; }
    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $d): static { $this->disponible = $d; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getReservations(): Collection { return $this->reservations; }
    public function getAvis(): Collection { return $this->avis; }

    public function getMoyenneNotes(): float
    {
        if ($this->avis->isEmpty()) return 0;
        $total = 0;
        foreach ($this->avis as $a) { $total += $a->getNote(); }
        return round($total / $this->avis->count(), 1);
    }
}
