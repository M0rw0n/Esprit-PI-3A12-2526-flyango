<?php
// ════════════════════════════════════════════════════════════════
//  FLY&GO — ENTITIES  (all in one file for easy copy-paste)
//  Split each class into its own file in src/Entity/
// ════════════════════════════════════════════════════════════════

// ──────────────────────────────
// src/Entity/Activity.php
// ──────────────────────────────
namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activity')]
class Activity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private int $capacity = 0;

    #[ORM\Column(name: 'place_id', nullable: true)]
    private ?int $placeId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    // Relation (lazy)
    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(name: 'place_id', referencedColumnName: 'id', nullable: true)]
    private ?Place $place = null;

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $t): static { $this->title = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getPrice(): float { return $this->price; }
    public function setPrice(float $p): static { $this->price = $p; return $this; }
    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(?string $d): static { $this->duration = $d; return $this; }
    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(?\DateTimeInterface $d): static { $this->date = $d; return $this; }
    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $c): static { $this->capacity = $c; return $this; }
    public function getPlaceId(): ?int { return $this->placeId; }
    public function setPlaceId(?int $p): static { $this->placeId = $p; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getPlace(): ?Place { return $this->place; }
    public function setPlace(?Place $p): static { $this->place = $p; return $this; }
}
