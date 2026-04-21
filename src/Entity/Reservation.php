<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation_hebergement')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_reservation', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hebergement::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_hebergement', referencedColumnName: 'id_hebergement')]
    private ?Hebergement $hebergement = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'nom_client', length: 100)]
    private string $nomClient = '';

    #[ORM\Column(name: 'email_client', length: 100)]
    private string $emailClient = '';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'telephone_client', length: 20, nullable: true)]
    private ?string $telephoneClient = null;

    #[ORM\Column(name: 'nombre_personnes', type: 'integer')]
    private int $nombrePersonnes = 1;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(name: 'nombre_nuits', type: 'integer')]
    private int $nombreNuits = 1;

    #[ORM\Column(name: 'montant_total', type: 'float')]
    private float $montantTotal = 0;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $statut = 'En attente';

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->dateDebut = new \DateTime();
        $this->dateFin = new \DateTime('+1 day');
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
    public function getDateDebut(): \DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $d): static { $this->dateDebut = $d; return $this; }
    public function getDateFin(): \DateTimeInterface { return $this->dateFin; }
    public function setDateFin(\DateTimeInterface $d): static { $this->dateFin = $d; return $this; }
    public function getNombreNuits(): int { return $this->nombreNuits; }
    public function setNombreNuits(int $n): static { $this->nombreNuits = max(1, $n); return $this; }
    public function getMontantTotal(): float { return $this->montantTotal; }
    public function setMontantTotal(float $m): static { $this->montantTotal = $m; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $s): static { $this->statut = $s; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
}
