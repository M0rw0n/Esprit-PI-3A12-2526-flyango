<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
class Booking
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'customer_name', length: 255)]
    private string $customerName = '';

    #[ORM\Column(name: 'client_phone', length: 50, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(length: 255)]
    private string $email = '';

    #[ORM\Column(name: 'activity_id')]
    private int $activityId = 0;

    #[ORM\Column]
    private int $persons = 1;

    #[ORM\Column(name: 'booking_date', type: 'date')]
    private \DateTimeInterface $bookingDate;

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    private float $totalPrice = 0;

    #[ORM\Column(length: 50)]
    private string $status = 'PENDING';

    #[ORM\Column(name: 'payment_status', length: 20)]
    private string $paymentStatus = 'EN ATTENTE';

    #[ORM\Column(name: 'payment_ref', length: 60, nullable: true)]
    private ?string $paymentRef = null;

    public function __construct()
    {
        $this->bookingDate = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomerName(): string { return $this->customerName; }
    public function setCustomerName(string $n): static { $this->customerName = $n; return $this; }
    public function getClientPhone(): ?string { return $this->clientPhone; }
    public function setClientPhone(?string $p): static { $this->clientPhone = $p; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): static { $this->email = $e; return $this; }
    public function getActivityId(): int { return $this->activityId; }
    public function setActivityId(int $a): static { $this->activityId = $a; return $this; }
    public function getPersons(): int { return $this->persons; }
    public function setPersons(int $p): static { $this->persons = $p; return $this; }
    public function getBookingDate(): \DateTimeInterface { return $this->bookingDate; }
    public function setBookingDate(\DateTimeInterface $d): static { $this->bookingDate = $d; return $this; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function setTotalPrice(float $p): static { $this->totalPrice = $p; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }
    public function getPaymentStatus(): string { return $this->paymentStatus; }
    public function setPaymentStatus(string $s): static { $this->paymentStatus = $s; return $this; }
    public function getPaymentRef(): ?string { return $this->paymentRef; }
    public function setPaymentRef(?string $r): static { $this->paymentRef = $r; return $this; }
}
