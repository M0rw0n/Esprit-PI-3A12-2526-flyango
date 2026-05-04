<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Enum\BookingStatusEnum;
use App\Enum\PaymentMethodEnum;
use App\Entity\Trait\BlameableTrait;
=======
>>>>>>> testsisi

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
class Booking
{
<<<<<<< HEAD
    use BlameableTrait;
=======
>>>>>>> testsisi
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'bookings')]
<<<<<<< HEAD
    #[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
=======
    #[ORM\JoinColumn(name: 'activity_id')]
>>>>>>> testsisi
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

<<<<<<< HEAD
    #[ORM\Column(name: 'status', type: 'booking_status_enum', length: 50, nullable: true)]
    private ?BookingStatusEnum $status = BookingStatusEnum::PENDING;
=======
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = 'PENDING';
>>>>>>> testsisi

    #[ORM\Column(name: 'payment_intent_id', length: 255, nullable: true)]
    private ?string $paymentIntentId = null;

<<<<<<< HEAD
    #[ORM\Column(name: 'payment_method', type: 'payment_method_enum', length: 50, nullable: true)]
    private ?PaymentMethodEnum $paymentMethod = null;
=======
    #[ORM\Column(name: 'payment_method', length: 50, nullable: true)]
    private ?string $paymentMethod = null;
>>>>>>> testsisi

    #[ORM\Column(name: 'booking_reference', length: 100, nullable: true)]
    private ?string $bookingReference = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->bookingDate = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
<<<<<<< HEAD
        $this->email = '';
=======
>>>>>>> testsisi
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
<<<<<<< HEAD
    public function setEmail(string $email): static { $this->email = $email; return $this; }
=======
    public function setEmail(string $e): static { $this->email = $e; return $this; }
>>>>>>> testsisi
    public function getPersons(): int { return $this->persons; }
    public function setPersons(int $p): static { $this->persons = max(1, $p); return $this; }
    public function getBookingDate(): \DateTimeInterface { return $this->bookingDate; }
    public function setBookingDate(\DateTimeInterface $d): static { $this->bookingDate = $d; return $this; }
    public function getTotalPrice(): float { return (float) $this->totalPrice; }
    public function setTotalPrice(float|string $p): static { $this->totalPrice = (string) $p; return $this; }
<<<<<<< HEAD
    public function getStatus(): ?BookingStatusEnum { return $this->status; }
    public function setStatus(?BookingStatusEnum $s): static { $this->status = $s; return $this; }
    public function getPaymentIntentId(): ?string { return $this->paymentIntentId; }
    public function setPaymentIntentId(?string $id): static { $this->paymentIntentId = $id; return $this; }
    public function getPaymentMethod(): ?PaymentMethodEnum { return $this->paymentMethod; }
    public function setPaymentMethod(?PaymentMethodEnum $m): static { $this->paymentMethod = $m; return $this; }
    public function getBookingReference(): ?string { return $this->bookingReference; }
    public function setBookingReference(?string $r): static { $this->bookingReference = $r; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
=======
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $s): static { $this->status = $s; return $this; }
    public function getPaymentIntentId(): ?string { return $this->paymentIntentId; }
    public function setPaymentIntentId(?string $id): static { $this->paymentIntentId = $id; return $this; }
    public function getPaymentMethod(): ?string { return $this->paymentMethod; }
    public function setPaymentMethod(?string $m): static { $this->paymentMethod = $m; return $this; }
    public function getBookingReference(): ?string { return $this->bookingReference; }
    public function setBookingReference(?string $r): static { $this->bookingReference = $r; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $d): static { $this->updatedAt = $d; return $this; }
>>>>>>> testsisi
}
