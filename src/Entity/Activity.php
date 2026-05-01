<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private string $price = '0';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer')]
    private int $capacity = 10;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Booking::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Review::class, cascade: ['persist', 'remove'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $t): static { $this->title = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getPrice(): string { return $this->price; }
    public function setPrice(string $p): static { $this->price = $p; return $this; }
    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(?string $d): static { $this->duration = $d; return $this; }
    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(?\DateTimeInterface $d): static { $this->date = $d; return $this; }
    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $c): static { $this->capacity = $c; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $l): static { $this->lieu = $l; return $this; }
    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $c): static { $this->category = $c; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $latitude): static { $this->latitude = $latitude; return $this; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $longitude): static { $this->longitude = $longitude; return $this; }
    public function getCoordinates(): array { return ['latitude' => $this->latitude, 'longitude' => $this->longitude]; }
    public function getRating(): ?float { return $this->rating; }
    public function setRating(?float $r): static { $this->rating = $r; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getBookings(): Collection { return $this->bookings; }
    public function getReviews(): Collection { return $this->reviews; }
    public function getNoteMoyenne(): float {
        if ($this->reviews->isEmpty()) return 0;
        $total = 0;
        foreach ($this->reviews as $r) { $total += $r->getRating(); }
        return round($total / $this->reviews->count(), 1);
    }
    public function getNbAvis(): int { return $this->reviews->count(); }
    public function getLocation(): ?string { return $this->lieu; }
}
