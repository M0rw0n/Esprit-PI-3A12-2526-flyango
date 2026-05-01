<?php

namespace App\Entity;

use App\Repository\PromoCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
#[ORM\Table(name: 'promo_code')]
class PromoCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le code promo est obligatoire')]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Min 3 caractères')]
    #[ORM\Column(length: 50, unique: true)]
    private string $code = '';

    #[Assert\NotBlank(message: 'Le type est obligatoire')]
    #[Assert\Choice(choices: ['percentage', 'fixed', 'free_night'], message: 'Type invalide')]
    #[ORM\Column(length: 20)]
    private string $type = '';

    #[Assert\Positive(message: 'La valeur doit être positive')]
    #[Assert\Type(type: 'float')]
    private float $value = 0;

    #[Assert\PositiveOrZero(message: 'Le nombre d\'utilisations doit être positif')]
    #[ORM\Column(name: 'max_uses', type: 'integer', nullable: true)]
    private ?int $maxUses = null;

    #[ORM\Column(name: 'used_count', type: 'integer')]
    private int $usedCount = 0;

    #[Assert\PositiveOrZero(message: 'Le nombre d\'utilisations par utilisateur doit être positif')]
    #[ORM\Column(name: 'max_uses_per_user', type: 'integer', nullable: true)]
    private ?int $maxUsesPerUser = null;

    #[ORM\Column(name: 'valid_from', type: 'date', nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(name: 'valid_until', type: 'date', nullable: true)]
    private ?\DateTimeInterface $validUntil = null;

    #[Assert\PositiveOrZero(message: 'Le nombre de nuits minimum doit être positif')]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minNuits = null;

    #[Assert\PositiveOrZero(message: 'Le montant minimum doit être positif')]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minAmount = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $hebergementType = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';
    const TYPE_FREE_NIGHT = 'free_night';

    public function __construct()
    {
        $this->usedCount = 0;
        $this->active = true;
    }

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function setCode(string $c): static { $this->code = strtoupper($c); return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getValue(): float { return $this->value; }
    public function setValue(float $v): static { $this->value = $v; return $this; }
    public function getMaxUses(): ?int { return $this->maxUses; }
    public function setMaxUses(?int $m): static { $this->maxUses = $m; return $this; }
    public function getUsedCount(): int { return $this->usedCount; }
    public function setUsedCount(int $c): static { $this->usedCount = $c; return $this; }
    public function getMaxUsesPerUser(): ?int { return $this->maxUsesPerUser; }
    public function setMaxUsesPerUser(?int $m): static { $this->maxUsesPerUser = $m; return $this; }
    public function getValidFrom(): ?\DateTimeInterface { return $this->validFrom; }
    public function setValidFrom(?\DateTimeInterface $d): static { $this->validFrom = $d; return $this; }
    public function getValidUntil(): ?\DateTimeInterface { return $this->validUntil; }
    public function setValidUntil(?\DateTimeInterface $d): static { $this->validUntil = $d; return $this; }
    public function getMinNuits(): ?int { return $this->minNuits; }
    public function setMinNuits(?int $n): static { $this->minNuits = $n; return $this; }
    public function getMinAmount(): ?int { return $this->minAmount; }
    public function setMinAmount(?int $m): static { $this->minAmount = $m; return $this; }
    public function getHebergementType(): ?string { return $this->hebergementType; }
    public function setHebergementType(?string $t): static { $this->hebergementType = $t; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $a): static { $this->active = $a; return $this; }

    public function isValid(\DateTimeInterface $now = null): bool
    {
        $now = $now ?? new \DateTime();
        
        if (!$this->active) return false;
        if ($this->maxUses && $this->usedCount >= $this->maxUses) return false;
        if ($this->validFrom && $this->validFrom > $now) return false;
        if ($this->validUntil && $this->validUntil < $now) return false;
        
        return true;
    }

    public function calculateDiscount(float $total, int $nuits, array $userUses = []): array
    {
        if (!$this->isValid()) {
            return ['discount' => 0, 'message' => 'Code promo invalide'];
        }
        
        if ($this->minNuits && $nuits < $this->minNuits) {
            return ['discount' => 0, 'message' => 'Minimum ' . $this->minNuits . ' nuits requises'];
        }
        
        if ($this->minAmount && $total < $this->minAmount) {
            return ['discount' => 0, 'message' => 'Minimum ' . $this->minAmount . ' TND requis'];
        }
        
        if ($this->maxUsesPerUser && isset($userUses[$this->code])) {
            if ($userUses[$this->code] >= $this->maxUsesPerUser) {
                return ['discount' => 0, 'message' => 'Limite utilisée pour ce code'];
            }
        }
        
        $discount = match($this->type) {
            self::TYPE_PERCENTAGE => $total * ($this->value / 100),
            self::TYPE_FIXED => min($this->value, $total),
            self::TYPE_FREE_NIGHT => 0,
            default => 0
        };
        
        $message = match($this->type) {
            self::TYPE_PERCENTAGE => '-' . $this->value . '%',
            self::TYPE_FIXED => '-' . $this->value . ' TND',
            self::TYPE_FREE_NIGHT => 'Nuit gratuite!',
            default => ''
        };
        
        return [
            'discount' => min($discount, $total),
            'message' => $message,
            'freeNights' => $this->type === self::TYPE_FREE_NIGHT ? 1 : 0
        ];
    }
}