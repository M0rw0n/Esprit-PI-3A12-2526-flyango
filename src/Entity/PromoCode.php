<?php

namespace App\Entity;

use App\Repository\PromoCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
#[ORM\Table(name: 'promo_code')]
class PromoCode
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $code = '';

    #[ORM\Column(length: 20)]
    private string $type = 'percentage';

    #[ORM\Column(type: 'float')]
    private float $value = 0;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $maxReduction = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $validUntil = null;

    #[ORM\Column(type: 'integer')]
    private int $usageLimit = 0;

    #[ORM\Column(type: 'integer')]
    private int $usedCount = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function setCode(string $c): static { $this->code = strtoupper($c); return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getValue(): float { return $this->value; }
    public function setValue(float $v): static { $this->value = $v; return $this; }
    public function getMaxReduction(): ?float { return $this->maxReduction; }
    public function setMaxReduction(?float $r): static { $this->maxReduction = $r; return $this; }
    public function getValidFrom(): ?\DateTimeInterface { return $this->validFrom; }
    public function setValidFrom(?\DateTimeInterface $d): static { $this->validFrom = $d; return $this; }
    public function getValidUntil(): ?\DateTimeInterface { return $this->validUntil; }
    public function setValidUntil(?\DateTimeInterface $d): static { $this->validUntil = $d; return $this; }
    public function getUsageLimit(): int { return $this->usageLimit; }
    public function setUsageLimit(int $l): static { $this->usageLimit = $l; return $this; }
    public function getUsedCount(): int { return $this->usedCount; }
    public function setUsedCount(int $c): static { $this->usedCount = $c; return $this; }
    public function incrementUsedCount(): static { $this->usedCount++; return $this; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    public function isValid(): bool
    {
        if (!$this->actif) return false;
        if ($this->usageLimit > 0 && $this->usedCount >= $this->usageLimit) return false;

        $now = new \DateTime();
        if ($this->validFrom && $now < $this->validFrom) return false;
        if ($this->validUntil && $now > $this->validUntil) return false;

        return true;
    }

    public function calculateReduction(float $originalPrice): float
    {
        if (!$this->isValid()) return 0;

        $reduction = $this->type === 'percentage'
            ? $originalPrice * ($this->value / 100)
            : $this->value;

        if ($this->maxReduction !== null) {
            $reduction = min($reduction, $this->maxReduction);
        }

        return round(min($reduction, $originalPrice), 2);
    }
}
