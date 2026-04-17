<?php

namespace App\Entity;

use App\Repository\TransportBookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransportBookingRepository::class)]
#[ORM\Table(name: 'booking_transport')]
class TransportBooking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'booking_id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TransportOffer::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'transport_id')]
    private ?TransportOffer $transportOffer = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id')]
    private ?User $user = null;

    #[ORM\Column(name: 'booking_date', type: 'datetime')]
    private \DateTimeInterface $bookingDate;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = 'PENDING';

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice = '0';

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $passengers = 1;

    #[ORM\Column(name: 'pickup_datetime', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $pickupDatetime = null;

    #[ORM\Column(name: 'dropoff_datetime', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dropoffDatetime = null;

    #[ORM\Column(name: 'travel_class', length: 50, nullable: true)]
    private ?string $travelClass = null;

    #[ORM\Column(name: 'cabin_bags', type: 'integer', nullable: true)]
    private ?int $cabinBags = 0;

    #[ORM\Column(name: 'checked_bags', type: 'integer', nullable: true)]
    private ?int $checkedBags = 0;

    #[ORM\Column(name: 'pickup_location', length: 150, nullable: true)]
    private ?string $pickupLocation = null;

    #[ORM\Column(name: 'dropoff_location', length: 150, nullable: true)]
    private ?string $dropoffLocation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customerName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $customerPhone = null;

    public function __construct()
    {
        $this->bookingDate = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTransportOffer(): ?TransportOffer { return $this->transportOffer; }
    public function setTransportOffer(?TransportOffer $o): static { $this->transportOffer = $o; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getBookingDate(): \DateTimeInterface { return $this->bookingDate; }
    public function setBookingDate(\DateTimeInterface $d): static { $this->bookingDate = $d; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $s): static { $this->status = $s; return $this; }
    public function getTotalPrice(): float { return (float) $this->totalPrice; }
    public function setTotalPrice(float|string $p): static { $this->totalPrice = (string) $p; return $this; }
    public function getPassengers(): ?int { return $this->passengers; }
    public function setPassengers(?int $p): static { $this->passengers = $p; return $this; }
    public function getPickupDatetime(): ?\DateTimeInterface { return $this->pickupDatetime; }
    public function setPickupDatetime(?\DateTimeInterface $d): static { $this->pickupDatetime = $d; return $this; }
    public function getDropoffDatetime(): ?\DateTimeInterface { return $this->dropoffDatetime; }
    public function setDropoffDatetime(?\DateTimeInterface $d): static { $this->dropoffDatetime = $d; return $this; }
    public function getTravelClass(): ?string { return $this->travelClass; }
    public function setTravelClass(?string $c): static { $this->travelClass = $c; return $this; }
    public function getCabinBags(): ?int { return $this->cabinBags; }
    public function setCabinBags(?int $b): static { $this->cabinBags = $b; return $this; }
    public function getCheckedBags(): ?int { return $this->checkedBags; }
    public function setCheckedBags(?int $b): static { $this->checkedBags = $b; return $this; }
    public function getPickupLocation(): ?string { return $this->pickupLocation; }
    public function setPickupLocation(?string $l): static { $this->pickupLocation = $l; return $this; }
    public function getDropoffLocation(): ?string { return $this->dropoffLocation; }
    public function setDropoffLocation(?string $l): static { $this->dropoffLocation = $l; return $this; }
    public function getCustomerName(): ?string { return $this->customerName; }
    public function setCustomerName(?string $n): static { $this->customerName = $n; return $this; }
    public function getCustomerEmail(): ?string { return $this->customerEmail; }
    public function setCustomerEmail(?string $e): static { $this->customerEmail = $e; return $this; }
    public function getCustomerPhone(): ?string { return $this->customerPhone; }
    public function setCustomerPhone(?string $p): static { $this->customerPhone = $p; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->bookingDate; }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'CONFIRMED' => 'success',
            'CANCELLED' => 'danger',
            'COMPLETED' => 'info',
            default => 'warning',
        };
    }
}
