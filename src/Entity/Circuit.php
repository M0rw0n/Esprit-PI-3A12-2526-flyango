<?php

namespace App\Entity;

use App\Repository\CircuitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

#[ORM\Entity(repositoryClass: CircuitRepository::class)]
#[ORM\Table(name: 'circuit')]
#[ApiResource(
    normalizationContext: ['groups' => ['circuit:read']],
    denormalizationContext: ['groups' => ['circuit:write']],
    paginationItemsPerPage: 20,
)]
#[ApiFilter(SearchFilter::class, properties: ['titre' => 'partial', 'destination' => 'partial', 'description' => 'partial'])]
#[ApiFilter(RangeFilter::class, properties: ['prix', 'noteMoyenne'])]
#[ApiFilter(OrderFilter::class, properties: ['prix', 'createdAt', 'noteMoyenne'], arguments: ['orderParameterName' => 'sort'])]
class Circuit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_circuit', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'title', length: 200)]
    private string $titre = '';

    #[Gedmo\Slug(fields: ['titre'])]
    #[ORM\Column(length: 200, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 150)]
    private string $destination = '';

    #[ORM\Column(name: 'start_date', type: 'date', nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(name: 'end_date', type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $budget = null;

    #[ORM\Column(name: 'image_url', type: 'text', nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'prix_par_personne', type: 'float')]
    private float $prix = 0;

    #[ORM\Column(name: 'note_moyenne', type: 'float', nullable: true)]
    private ?float $noteMoyenne = null;

    #[ORM\Column(name: 'nb_avis', type: 'integer', nullable: true)]
    private ?int $nbAvis = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $difficulte = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $depart = null;

    #[ORM\Column(name: 'places_disponibles', type: 'integer', nullable: true)]
    private ?int $placesDisponibles = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'is_custom', type: 'boolean')]
    private bool $isCustom = false;

    #[ORM\Column(name: 'is_ai_generated', type: 'boolean')]
    private bool $isAiGenerated = false;

    #[ORM\Column(name: 'source_type', length: 50)]
    private string $sourceType = 'admin';

    #[ORM\Column(name: 'generated_context', type: 'text', nullable: true)]
    private ?string $generatedContext = null;

    #[ORM\Column(name: 'promo_prix', type: 'float', nullable: true)]
    private ?float $promoPrix = null;

    #[ORM\Column(name: 'promo_start', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $promoStart = null;

    #[ORM\Column(name: 'promo_end', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $promoEnd = null;

    #[ORM\Column(name: 'plan_b', type: 'text', nullable: true)]
    private ?string $planB = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: true)]
    private ?User $creator = null;

    #[ORM\OneToMany(mappedBy: 'circuit', targetEntity: ReservationCircuit::class, cascade: ['remove'])]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'circuit', targetEntity: CircuitAvis::class, cascade: ['remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $t): static { $this->titre = $t; return $this; }
    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(?string $s): static { $this->slug = $s; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(?string $t): static { $this->type = $t; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $s): static { $this->status = $s; return $this; }
    public function getDestination(): string { return $this->destination; }
    public function setDestination(string $d): static { $this->destination = $d; return $this; }
    public function getStartDate(): ?\DateTimeInterface { return $this->startDate; }
    public function setStartDate(?\DateTimeInterface $d): static { $this->startDate = $d; return $this; }
    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }
    public function setEndDate(?\DateTimeInterface $d): static { $this->endDate = $d; return $this; }
    public function getDuree(): ?int { return $this->duree; }
    public function setDuree(?int $d): static { $this->duree = $d; return $this; }
    public function getBudget(): ?float { return $this->budget; }
    public function setBudget(?float $b): static { $this->budget = $b; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $p): static { $this->prix = $p; return $this; }
    public function getNoteMoyenne(): ?float { return $this->noteMoyenne; }
    public function setNoteMoyenne(?float $n): static { $this->noteMoyenne = $n; return $this; }
    public function getNbAvis(): ?int { return $this->nbAvis; }
    public function setNbAvis(?int $n): static { $this->nbAvis = $n; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function getDifficulte(): ?string { return $this->difficulte; }
    public function setDifficulte(?string $d): static { $this->difficulte = $d; return $this; }
    public function getDepart(): ?string { return $this->depart; }
    public function setDepart(?string $d): static { $this->depart = $d; return $this; }
    public function getPlacesDisponibles(): ?int { return $this->placesDisponibles; }
    public function setPlacesDisponibles(?int $p): static { $this->placesDisponibles = $p; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
    public function isCustom(): bool { return $this->isCustom; }
    public function setIsCustom(bool $c): static { $this->isCustom = $c; return $this; }
    public function isAiGenerated(): bool { return $this->isAiGenerated; }
    public function setIsAiGenerated(bool $g): static { $this->isAiGenerated = $g; return $this; }
    public function getSourceType(): string { return $this->sourceType; }
    public function setSourceType(string $s): static { $this->sourceType = $s; return $this; }
    public function getGeneratedContext(): ?string { return $this->generatedContext; }
    public function setGeneratedContext(?string $c): static { $this->generatedContext = $c; return $this; }
    public function getCreator(): ?User { return $this->creator; }
    public function setCreator(?User $c): static { $this->creator = $c; return $this; }
    public function getReservations(): Collection { return $this->reservations; }
    public function getAvis(): Collection { return $this->avis; }

    public function getMoyenneNotes(): float
    {
        if ($this->avis->isEmpty()) return 0;
        $total = 0;
        foreach ($this->avis as $avis) { $total += $avis->getRating(); }
        return round($total / $this->avis->count(), 1);
    }

    public function getPromoPrix(): ?float { return $this->promoPrix; }
    public function setPromoPrix(?float $p): static { $this->promoPrix = $p; return $this; }
    public function getPromoStart(): ?\DateTimeInterface { return $this->promoStart; }
    public function setPromoStart(?\DateTimeInterface $d): static { $this->promoStart = $d; return $this; }
    public function getPromoEnd(): ?\DateTimeInterface { return $this->promoEnd; }
    public function setPromoEnd(?\DateTimeInterface $d): static { $this->promoEnd = $d; return $this; }

    public function getPlanB(): ?string { return $this->planB; }
    public function setPlanB(?string $p): static { $this->planB = $p; return $this; }

    public function hasActivePromo(): bool
    {
        if (!$this->promoPrix) return false;
        $now = new \DateTime();
        if ($this->promoStart && $now < $this->promoStart) return false;
        if ($this->promoEnd && $now > $this->promoEnd) return false;
        return $this->promoPrix < $this->prix;
    }

    public function getReductionPercent(): int
    {
        if (!$this->hasActivePromo()) return 0;
        return (int) round((1 - $this->promoPrix / $this->prix) * 100);
    }
}
