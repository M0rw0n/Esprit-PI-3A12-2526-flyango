<?php

namespace App\Entity;

use App\Repository\HebergementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 3, max: 150, minMessage: 'Min 3 caractères', maxMessage: 'Max 150 caractères')]
    #[ORM\Column(length: 100)]
    private string $nom = '';

    #[Assert\NotBlank(message: 'La ville est obligatoire')]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(length: 50)]
    private string $ville = '';

    #[Assert\NotBlank(message: 'Le type est obligatoire')]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private string $type = '';

    #[Assert\Range(min: 0, max: 10000, notInRangeMessage: 'Le prix doit être entre {{ min }} et {{ max }} TND')]
    #[ORM\Column(name: 'prix_par_nuit', type: 'float')]
    private float $prixParNuit = 0;

    #[Assert\Length(max: 500)]
    #[ORM\Column(name: 'image_path', length: 500, nullable: true)]
    private ?string $image = null;

    #[Assert\Length(max: 1000, maxMessage: 'Max 1000 caractères')]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Assert\Length(max: 200)]
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $adresse = null;

    #[Assert\Range(min: 1, max: 50, notInRangeMessage: 'La capacité doit être entre {{ min }} et {{ max }}')]
    #[ORM\Column(name: 'nombre_chambres', type: 'integer', nullable: true)]
    private ?int $capacite = null;

    #[ORM\Column(type: 'boolean')]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'hebergement', targetEntity: Reservation::class, cascade: ['remove'])]
    private Collection $reservations;

    #[ORM\Column(type: 'json')]
    private array $blockedDates = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $chambresDisponibles = null;

    #[ORM\Column(type: 'json')]
    private array $galeriePhotos = [];

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(type: 'integer')]
    private int $vues = 0;

    #[ORM\Column(type: 'json')]
    private array $photos360 = [];

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $note = null;

    #[ORM\Column(type: 'json')]
    private array $equipements = [];

    #[ORM\Column(name: 'amadeus_id', type: 'string', length: 50, nullable: true)]
    private ?string $amadeusId = null;

    #[ORM\OneToMany(mappedBy: 'hebergement', targetEntity: Avis::class, cascade: ['remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->galeriePhotos = [];
        $this->equipements = [];
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
    public function getBlockedDates(): array { return $this->blockedDates; }
    public function setBlockedDates(array $dates): static { $this->blockedDates = $dates; return $this; }
    public function addBlockedDate(string $date): static { 
        if (!in_array($date, $this->blockedDates)) {
            $this->blockedDates[] = $date;
        }
        return $this; 
    }
    public function getChambresDisponibles(): ?int { return $this->chambresDisponibles; }
    public function setChambresDisponibles(?int $n): static { $this->chambresDisponibles = $n; return $this; }
    public function getGaleriePhotos(): array { return $this->galeriePhotos; }
    public function setGaleriePhotos(array $photos): static { $this->galeriePhotos = $photos; return $this; }
    public function addGaleriePhoto(string $photo): static { 
        $this->galeriePhotos[] = $photo;
        return $this; 
    }
    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $lat): static { $this->latitude = $lat; return $this; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $lng): static { $this->longitude = $lng; return $this; }
    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(?string $loc): static { $this->localisation = $loc; return $this; }
    public function getVues(): int { return $this->vues; }
    public function setVues(int $v): static { $this->vues = $v; return $this; }
    public function incrementVues(): static { $this->vues++; return $this; }
    public function getAvis(): Collection { return $this->avis; }
    public function getNote(): ?float { return $this->note; }
    public function setNote(?float $note): static { $this->note = $note; return $this; }
    public function getEquipements(): array { return $this->equipements; }
    public function setEquipements(array $equipements): static { $this->equipements = $equipements; return $this; }
    public function getAmadeusId(): ?string { return $this->amadeusId; }
    public function setAmadeusId(?string $id): static { $this->amadeusId = $id; return $this; }
    public function getPhotos360(): array { return $this->photos360; }
    public function setPhotos360(array $photos): static { $this->photos360 = $photos; return $this; }
    public function addPhoto360(string $photo): static { 
        $this->photos360[] = $photo;
        return $this; 
    }
    public function removePhoto360(string $photo): static {
        if (($key = array_search($photo, $this->photos360)) !== false) {
            unset($this->photos360[$key]);
            $this->photos360 = array_values($this->photos360);
        }
        return $this;
    }

    public function getMoyenneNotes(): float
    {
        if ($this->avis->isEmpty()) return 0;
        $total = 0;
        foreach ($this->avis as $a) { $total += $a->getNote(); }
        return round($total / $this->avis->count(), 1);
    }

    public function getCapaciteRestante(): int
    {
        $used = 0;
        $today = new \DateTime();
        foreach ($this->reservations as $r) {
            $statut = method_exists($r, 'getStatut') ? $r->getStatut() : $r->getStatus();
            if (in_array($statut, ['confirme', 'CONFIRME', 'confirmé']) && $r->getDateFin() >= $today) {
                $used += $r->getNombrePersonnes();
            }
        }
        $total = $this->capacite ?? 0;
        return max(0, $total - $used);
    }
}