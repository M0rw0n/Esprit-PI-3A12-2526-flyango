<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(name: 'role', length: 50, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(name: 'mot_de_passe', length: 255)]
    private string $password = '';

    #[ORM\Column(length: 100)]
    private string $nom = '';

    #[ORM\Column(length: 100)]
    private string $prenom = '';

    #[ORM\Column(name: 'phone', length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'profile_picture_path', length: 500, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->role = 'ROLE_USER';
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): static { $this->email = $e; return $this; }
    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->role) {
            $role = $this->role;
            if (!str_starts_with($role, 'ROLE_')) {
                $role = 'ROLE_' . $role;
            }
            $roles[] = $role;
        }
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    public function setRoles(array $r): static
    {
        $this->role = $r[0] ?? 'ROLE_USER';
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getRole(): ?string { return $this->role; }
    public function setRole(?string $r): static { $this->role = $r; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $p): static { $this->password = $p; return $this; }
    public function eraseCredentials(): void {}
    public function getNom(): string { return $this->nom; }
    public function setNom(string $n): static { $this->nom = $n; return $this; }
    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $p): static { $this->prenom = $p; return $this; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $t): static { $this->telephone = $t; return $this; }
    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $a): static { $this->avatar = $a; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function isActive(): bool { return $this->actif; }
    public function isActif(): bool { return $this->actif; }
    public function setIsActive(bool $v): static { $this->actif = $v; return $this; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
    public function getFullName(): string { return trim($this->prenom . ' ' . $this->nom); }
}
