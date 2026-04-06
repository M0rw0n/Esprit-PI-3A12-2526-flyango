<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(name: 'activity_id')]
    private ?Activity $activity = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'customer_name', length: 255)]
    private string $customerName = '';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(length: 255)]
    private string $email = '';

    #[ORM\Column(type: 'integer')]
    private int $persons = 1;

    #[ORM\Column(name: 'booking_date', type: 'date')]
    private \DateTimeInterface $bookingDate;

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice = '0';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = 'PENDING';

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->bookingDate = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getActivity(): ?Activity { return $this->activity; }
    public function setActivity(?Activity $a): static { $this->activity = $a; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getCustomerName(): string { return $this->customerName; }
    public function setCustomerName(string $n): static { $this->customerName = $n; return $this; }
    public function getClientPhone(): ?string { return $this->clientPhone; }
    public function setClientPhone(?string $p): static { $this->clientPhone = $p; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): static { $this->email = $e; return $this; }
    public function getPersons(): int { return $this->persons; }
    public function setPersons(int $p): static { $this->persons = max(1, $p); return $this; }
    public function getBookingDate(): \DateTimeInterface { return $this->bookingDate; }
    public function setBookingDate(\DateTimeInterface $d): static { $this->bookingDate = $d; return $this; }
    public function getTotalPrice(): float { return (float) $this->totalPrice; }
    public function setTotalPrice(float|string $p): static { $this->totalPrice = (string) $p; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $s): static { $this->status = $s; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $d): static { $this->updatedAt = $d; return $this; }
}
