<?php

namespace App\Entity;

use App\Repository\ProfilVoyageurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Trait\BlameableTrait;

#[ORM\Entity(repositoryClass: ProfilVoyageurRepository::class)]
#[ORM\Table(name: 'profil_voyageur')]
#[ORM\HasLifecycleCallbacks]
class ProfilVoyageur
{
    use BlameableTrait;
    public const TYPE_ADVENTURE = 'Adventure';
    public const TYPE_RELAXATION = 'Relaxation';
    public const TYPE_CULTURAL = 'Cultural';
    public const TYPE_BUSINESS = 'Business';
    public const TYPE_FAMILY = 'Family';
    public const TYPE_ROMANTIC = 'Romantic';

    public const TYPES = [
        self::TYPE_ADVENTURE,
        self::TYPE_RELAXATION,
        self::TYPE_CULTURAL,
        self::TYPE_BUSINESS,
        self::TYPE_FAMILY,
        self::TYPE_ROMANTIC,
    ];

    public const TYPE_LABELS = [
        self::TYPE_ADVENTURE => 'Aventure',
        self::TYPE_RELAXATION => 'Relaxation',
        self::TYPE_CULTURAL => 'Culturel',
        self::TYPE_BUSINESS => 'Affaires',
        self::TYPE_FAMILY => 'Famille',
        self::TYPE_ROMANTIC => 'Romantique',
    ];

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profilVoyageur', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(name: 'destination_preferee', length: 255)]
    #[Assert\NotBlank(message: 'La destination préférée est requise.')]
    #[Assert\Length(max: 255, maxMessage: 'La destination ne peut pas dépasser {{ limit }} caractères.')]
    private string $destinationPreferee = '';

    #[ORM\Column(name: 'type_voyage', length: 50)]
    #[Assert\NotBlank(message: 'Le type de voyage est requis.')]
    #[Assert\Choice(choices: self::TYPES, message: 'Type de voyage invalide.')]
    private string $typeVoyage = self::TYPE_ADVENTURE;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotNull(message: 'Le budget est requis.')]
    #[Assert\Positive(message: 'Le budget doit être positif.')]
    private ?string $budget = '0.00';

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /** @var \DateTimeInterface Transient, not mapped */
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getDestinationPreferee(): string { return $this->destinationPreferee; }
    public function setDestinationPreferee(string $d): static { $this->destinationPreferee = $d; return $this; }
    public function getTypeVoyage(): string { return $this->typeVoyage; }
    public function setTypeVoyage(string $t): static { $this->typeVoyage = $t; return $this; }
    public function getBudget(): float { return (float) ($this->budget ?? 0); }
    public function setBudget(float|int|string|null $b): static { $this->budget = (string) $b; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }

    public function getTypeVoyageLabel(): string
    {
        return self::TYPE_LABELS[$this->typeVoyage] ?? $this->typeVoyage;
    }

    public function getTypeVoyageIcon(): string
    {
        return match($this->typeVoyage) {
            self::TYPE_ADVENTURE => 'fa-mountain',
            self::TYPE_RELAXATION => 'fa-spa',
            self::TYPE_CULTURAL => 'fa-landmark',
            self::TYPE_BUSINESS => 'fa-briefcase',
            self::TYPE_FAMILY => 'fa-users',
            self::TYPE_ROMANTIC => 'fa-heart',
            default => 'fa-plane',
        };
    }
}
