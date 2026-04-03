<?php

namespace App\Entity;

use App\Repository\ProfilVoyageurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: ProfilVoyageurRepository::class)]
#[ORM\Table(name: 'profil_voyageur')]
class ProfilVoyageur
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'profilVoyageur', targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $destinationPreferee = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $typeVoyage = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $budget = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDestinationPreferee(): ?string
    {
        return $this->destinationPreferee;
    }

    public function setDestinationPreferee(string $destinationPreferee): static
    {
        $this->destinationPreferee = $destinationPreferee;
        return $this;
    }

    public function getTypeVoyage(): ?string
    {
        return $this->typeVoyage;
    }

    public function setTypeVoyage(string $typeVoyage): static
    {
        $this->typeVoyage = $typeVoyage;
        return $this;
    }

    public function getTypeVoyageIcon(): string
    {
        return match($this->typeVoyage) {
            'Adventure' => '🏔️',
            'Relaxation' => '🏖️',
            'Cultural' => '🏛️',
            'Business' => '💼',
            'Family' => '👨‍👩‍👧',
            'Romantic' => '💑',
            default => '✈️',
        };
    }

    public function getTypeVoyageClass(): string
    {
        return match($this->typeVoyage) {
            'Adventure' => 'badge-adventure',
            'Relaxation' => 'badge-relaxation',
            'Cultural' => 'badge-cultural',
            'Business' => 'badge-business',
            'Family' => 'badge-family',
            'Romantic' => 'badge-romantic',
            default => 'badge-adventure',
        };
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(string $budget): static
    {
        $this->budget = $budget;
        return $this;
    }

    public function getBudgetFormate(): string
    {
        return number_format((float)$this->budget, 0, ',', ' ') . '€';
    }
}
