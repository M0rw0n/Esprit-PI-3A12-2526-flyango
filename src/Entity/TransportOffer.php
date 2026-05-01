<?php

namespace App\Entity;

use App\Repository\TransportOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TransportTypeEnum;

#[ORM\Entity(repositoryClass: TransportOfferRepository::class)]
#[ORM\Table(name: 'transport_details')]
class TransportOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'transport_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'offer_id')]
    private int $offerId = 0;

    #[ORM\Column(name: 'transport_type', type: 'transport_type_enum', length: 50, nullable: true)]
    private ?TransportTypeEnum $transportType = null;

    #[ORM\Column(name: 'company_name', length: 150, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'departure_city', length: 100, nullable: true)]
    private ?string $departureCity = null;

    #[ORM\Column(name: 'arrival_city', length: 100, nullable: true)]
    private ?string $arrivalCity = null;

    #[ORM\Column(name: 'departure_datetime', type: 'datetime')]
    private \DateTimeInterface $departureDatetime;

    #[ORM\Column(name: 'arrival_datetime', type: 'datetime')]
    private \DateTimeInterface $arrivalDatetime;

    #[ORM\Column(name: 'available_seats', type: 'integer', nullable: true)]
    private ?int $availableSeats = 0;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $price = '0';

    #[ORM\Column(name: 'departure_station', length: 100, nullable: true)]
    private ?string $departureStation = null;

    #[ORM\Column(name: 'arrival_station', length: 100, nullable: true)]
    private ?string $arrivalStation = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $amenities = null;

    #[ORM\Column(name: 'image_path', length: 500, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'transportOffer', targetEntity: TransportBooking::class, cascade: ['remove'])]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'transportOffer', targetEntity: TransportAvis::class)]
    private Collection $avis;

    public function __construct()
    {
        $this->departureDatetime = new \DateTime();
        $this->arrivalDatetime = new \DateTime('+2 hours');
        $this->createdAt = new \DateTime();
        $this->bookings = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getOfferId(): int { return $this->offerId; }
    public function setOfferId(int $id): static { $this->offerId = $id; return $this; }
    public function getTransportType(): ?TransportTypeEnum { return $this->transportType; }
    public function setTransportType(?TransportTypeEnum $t): static { $this->transportType = $t; return $this; }
    public function getCompanyName(): ?string { return $this->companyName; }
    public function setCompanyName(?string $n): static { $this->companyName = $n; return $this; }
    public function getDepartureCity(): ?string { return $this->departureCity; }
    public function setDepartureCity(?string $c): static { $this->departureCity = $c; return $this; }
    public function getArrivalCity(): ?string { return $this->arrivalCity; }
    public function setArrivalCity(?string $c): static { $this->arrivalCity = $c; return $this; }
    public function getDepartureDatetime(): \DateTimeInterface { return $this->departureDatetime; }
    public function getArrivalDatetime(): \DateTimeInterface { return $this->arrivalDatetime; }
    public function getAvailableSeats(): ?int { return $this->availableSeats; }
    public function setAvailableSeats(?int $s): static { $this->availableSeats = $s; return $this; }
    public function getPrice(): float { return $this->price !== null ? (float) $this->price : 0.0; }
    public function setPrice(float|string|null $p): static { $this->price = $p !== null ? (string) $p : null; return $this; }
    public function getDepartureStation(): ?string { return $this->departureStation; }
    public function setDepartureStation(?string $s): static { $this->departureStation = $s; return $this; }
    public function getArrivalStation(): ?string { return $this->arrivalStation; }
    public function setArrivalStation(?string $s): static { $this->arrivalStation = $s; return $this; }
    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(?string $d): static { $this->duration = $d; return $this; }
    public function getAmenities(): ?string { return $this->amenities; }
    public function setAmenities(?string $a): static { $this->amenities = $a; return $this; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function setImagePath(?string $i): static { $this->imagePath = $i; return $this; }
    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $a): static { $this->isActive = $a; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getBookings(): Collection { return $this->bookings; }
    public function getAvis(): Collection { return $this->avis; }

    public function getRoute(): string
    {
        return ($this->departureCity ?? '') . ' → ' . ($this->arrivalCity ?? '');
    }

    public function getTransportIcon(): string
    {
        return match($this->transportType) {
            TransportTypeEnum::FLIGHT, TransportTypeEnum::AVION => 'fa-plane',
            TransportTypeEnum::TRAIN, TransportTypeEnum::TRAIN_FR => 'fa-train',
            TransportTypeEnum::BUS, TransportTypeEnum::BUS_FR => 'fa-bus',
            TransportTypeEnum::CAR, TransportTypeEnum::TAXI, TransportTypeEnum::TAXI_FR => 'fa-car',
            default => 'fa-ship',
        };
    }
}
