<?php

namespace App\Entity;

use App\Repository\ReservationCircuitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationCircuitRepository::class)]
#[ORM\Table(name: 'circuit_reservation')]
class ReservationCircuit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Circuit::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_circuit', referencedColumnName: 'id_circuit')]
    private ?Circuit $circuit = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'nb_travelers', type: 'integer', nullable: true)]
    private int $nbPersonnes = 1;

    #[ORM\Column(name: 'total_price', type: 'float', nullable: true)]
    private ?float $montantTotal = 0;

    #[ORM\Column(name: 'status', length: 30, nullable: true)]
    private ?string $statut = 'CONFIRME';

    #[ORM\Column(name: 'reserved_at', type: 'datetime')]
    private \DateTimeInterface $dateReservation;

    #[ORM\Column(name: 'date_depart', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(name: 'pdf_path', length: 500, nullable: true)]
    private ?string $pdfPath = null;

    #[ORM\Column(name: 'qr_path', length: 500, nullable: true)]
    private ?string $qrPath = null;

    #[ORM\Column(name: 'nom_client', length: 100, nullable: true)]
    private ?string $nomClient = null;

    #[ORM\Column(name: 'email_client', length: 100, nullable: true)]
    private ?string $emailClient = null;

    #[ORM\Column(name: 'telephone', length: 20, nullable: true)]
    private ?string $telephone = null;

    public function __construct()
    {
        $this->dateReservation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getCircuit(): ?Circuit { return $this->circuit; }
    public function setCircuit(?Circuit $c): static { $this->circuit = $c; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): static { $this->user = $u; return $this; }
    public function getNbPersonnes(): int { return $this->nbPersonnes; }
    public function setNbPersonnes(int $n): static { $this->nbPersonnes = max(1, $n); return $this; }
    public function getMontantTotal(): ?float { return $this->montantTotal; }
    public function setMontantTotal(?float $m): static { $this->montantTotal = $m; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $s): static { $this->statut = $s; return $this; }
    public function getDateReservation(): \DateTimeInterface { return $this->dateReservation; }
    public function setDateReservation(\DateTimeInterface $d): static { $this->dateReservation = $d; return $this; }
    public function getDateDepart(): ?\DateTimeInterface { return $this->dateDepart; }
    public function setDateDepart(?\DateTimeInterface $d): static { $this->dateDepart = $d; return $this; }
    public function getPdfPath(): ?string { return $this->pdfPath; }
    public function setPdfPath(?string $p): static { $this->pdfPath = $p; return $this; }
    public function getQrPath(): ?string { return $this->qrPath; }
    public function setQrPath(?string $q): static { $this->qrPath = $q; return $this; }
    public function getNomClient(): ?string { return $this->nomClient; }
    public function setNomClient(?string $n): static { $this->nomClient = $n; return $this; }
    public function getEmailClient(): ?string { return $this->emailClient; }
    public function setEmailClient(?string $e): static { $this->emailClient = $e; return $this; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $t): static { $this->telephone = $t; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->dateReservation; }
}
