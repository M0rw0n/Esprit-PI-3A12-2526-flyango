<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PaymentMethodEnum;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation_hebergement')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_reservation', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hebergement::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'hebergement_id', referencedColumnName: 'id_hebergement', onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Hébergement obligatoire')]
    private ?Hebergement $hebergement = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(name: 'nom_client', length: 100)]
    private string $nomClient = '';

    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'Email invalide')]
    #[ORM\Column(name: 'email_client', length: 100)]
private string $emailClient = '';

    #[Assert\Regex(pattern: '/^[0-9+\s\-()]{8,20}$/', message: 'Téléphone invalide')]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[Assert\Regex(pattern: '/^[0-9+\s\-()]{8,20}$/', message: 'Téléphone invalide')]
    #[ORM\Column(name: 'telephone_client', length: 20, nullable: true)]
    private ?string $telephoneClient = null;

    #[Assert\NotNull(message: 'Le nombre de personnes est obligatoire')]
    #[Assert\Range(min: 1, max: 20, notInRangeMessage: 'Le nombre de personnes doit être entre {{ min }} et {{ max }}')]
    #[ORM\Column(name: 'nombre_personnes', type: 'integer')]
    private int $nombrePersonnes = 1;
    
    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 10, notInRangeMessage: 'Le nombre de chambres doit être entre {{ min }} et {{ max }}')]
    private int $nombreChambres = 1;

    #[Assert\NotNull(message: 'La date de début est obligatoire')]
    #[ORM\Column(name: 'date_debut', type: 'date')]
    private \DateTimeInterface $dateDebut;

    #[Assert\NotNull(message: 'La date de fin est obligatoire')]
    #[ORM\Column(name: 'date_fin', type: 'date')]
    private \DateTimeInterface $dateFin;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 365, notInRangeMessage: 'Le nombre de nuits doit être entre {{ min }} et {{ max }}')]
    #[ORM\Column(name: 'nombre_nuits', type: 'integer')]
    private int $nombreNuits = 1;

    #[Assert\Positive(message: 'Le montant doit être positif')]
    #[ORM\Column(name: 'montant_total', type: 'decimal', precision: 10, scale: 2)]
    private string $montantTotal = '0';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $statut = 'EN_ATTENTE';

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    /** @var User|null Transient, not mapped */
    private ?User $createdBy = null;

    /** @var User|null Transient, not mapped */
    private ?User $updatedBy = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $paymentId = null;

    #[ORM\Column(name: 'payment_method', type: 'payment_method_enum', length: 50, nullable: true)]
    private ?PaymentMethodEnum $paymentMethod = null;

    #[ORM\Column(name: 'paid_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(name: 'facture_pdf', length: 500, nullable: true)]
    private ?string $facturePdf = null;

    #[ORM\Column(name: 'qr_code', length: 500, nullable: true)]
    private ?string $qrCode = null;

    const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    const STATUT_CONFIRMEE = 'CONFIRMEE';
    const STATUT_ANNULEE = 'ANNULEE';
    const STATUT_TERMINEE = 'TERMINEE';

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->dateDebut = new \DateTime();
        $this->dateFin = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getHebergement(): ?Hebergement { return $this->hebergement; }
    public function setHebergement(?Hebergement $h): static { $this->hebergement = $h; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getNomClient(): string { return $this->nomClient; }
    public function setNomClient(string $n): static { $this->nomClient = $n; return $this; }
    public function getEmailClient(): string { return $this->emailClient; }
    public function setEmailClient(string $e): static { $this->emailClient = $e; return $this; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $t): static { $this->telephone = $t; return $this; }
    public function getTelephoneClient(): ?string { return $this->telephoneClient; }
    public function setTelephoneClient(?string $t): static { $this->telephoneClient = $t; return $this; }
    public function getNombrePersonnes(): int { return $this->nombrePersonnes; }
    public function setNombrePersonnes(int $n): static { $this->nombrePersonnes = max(1, $n); return $this; }
    public function getNombreChambres(): int { return $this->nombreChambres; }
    public function setNombreChambres(int $n): static { $this->nombreChambres = max(1, $n); return $this; }
    public function getDateDebut(): \DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $d): static { $this->dateDebut = $d; return $this; }
    public function getDateFin(): \DateTimeInterface { return $this->dateFin; }
    public function setDateFin(\DateTimeInterface $d): static { $this->dateFin = $d; return $this; }
    public function getNombreNuits(): int { return $this->nombreNuits; }
    public function setNombreNuits(int $n): static { $this->nombreNuits = max(1, $n); return $this; }
    public function getMontantTotal(): string { return $this->montantTotal; }
    public function setMontantTotal(string $m): static { $this->montantTotal = $m; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $s): static { $this->statut = $s; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getPaymentId(): ?string { return $this->paymentId; }
    public function setPaymentId(?string $p): static { $this->paymentId = $p; return $this; }
    public function getPaymentMethod(): ?PaymentMethodEnum { return $this->paymentMethod; }
    public function setPaymentMethod(?PaymentMethodEnum $p): static { $this->paymentMethod = $p; return $this; }
    public function getPaidAt(): ?\DateTimeInterface { return $this->paidAt; }
    public function setPaidAtFromController(): void { $this->paidAt = new \DateTime(); }
    public function getFacturePdf(): ?string { return $this->facturePdf; }
    public function setFacturePdf(?string $f): static { $this->facturePdf = $f; return $this; }
    public function getQrCode(): ?string { return $this->qrCode; }
    public function setQrCode(?string $q): static { $this->qrCode = $q; return $this; }
}
