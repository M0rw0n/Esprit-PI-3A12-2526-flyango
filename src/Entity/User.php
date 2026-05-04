<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\ProfilVoyageur;
use App\Entity\Story;
use App\Entity\Circuit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Security\Core\Attribute\SensitiveParameter;
use App\Enum\UserRoleEnum;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
=======
use App\Entity\ProfilVoyageur;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
>>>>>>> testsisi
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['facebookId'])]
#[UniqueEntity(fields: ['googleId'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', length: 180, unique: true)]
<<<<<<< HEAD
    private ?string $email = null;

    #[ORM\Column(name: 'role', type: 'user_role_enum', length: 50, nullable: true)]
    private ?UserRoleEnum $role = null;

    #[ORM\Column(name: 'mot_de_passe', length: 255)]
    #[Ignore]
=======
    private string $email = '';

    #[ORM\Column(name: 'role', length: 50, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(name: 'mot_de_passe', length: 255)]
>>>>>>> testsisi
    private string $password = '';

    #[ORM\Column(length: 100)]
    private string $nom = '';

    #[ORM\Column(length: 100)]
    private string $prenom = '';

    #[ORM\Column(name: 'facebook_id', length: 100, nullable: true)]
    private ?string $facebookId = null;

    #[ORM\Column(name: 'google_id', length: 100, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(name: 'phone', length: 20, nullable: true)]
    private ?string $telephone = null;

<<<<<<< HEAD
    #[ORM\Column(name: 'profile_picture_path', length: 500, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(name: 'cover_photo_path', length: 500, nullable: true)]
    private ?string $coverPhoto = null;

    #[ORM\Column(name: 'auth_provider', length: 50, nullable: true)]
    private ?string $authProvider = null;

    #[ORM\Column(name: 'external_id', length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    /** @var Collection<int, Circuit> */
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Circuit::class)]
    private Collection $circuits;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: ProfilVoyageur::class, cascade: ['persist', 'remove'])]
    private ?ProfilVoyageur $profilVoyageur = null;

    /** @var Collection<int, Story> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Story::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $stories;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastSeenAt = null;

    #[Ignore]
    #[ORM\Column(name: 'reset_token', length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[Ignore]
    #[ORM\Column(name: 'reset_token_expires_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    public function __construct()
    {
        $this->circuits = new ArrayCollection();
        $this->stories = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->role = UserRoleEnum::tryFrom('ROLE_USER') ?? UserRoleEnum::ROLE_USER;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getUserIdentifier(): string { return $this->email ?? ''; }
=======
#[ORM\Column(name: 'profile_picture_path', length: 500, nullable: true)]
private ?string $avatar = null;

#[ORM\Column(name: 'reset_token', length: 255, nullable: true)]
private ?string $resetToken = null;

#[ORM\Column(name: 'reset_token_expires_at', type: 'datetime', nullable: true)]
private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'last_active_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastActiveAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: ProfilVoyageur::class, cascade: ['persist', 'remove'])]
    private ?ProfilVoyageur $profilVoyageur = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->role = 'ROLE_USER';
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): static { $this->email = $e; return $this; }
    public function getUserIdentifier(): string { return $this->email; }
>>>>>>> testsisi

    public function getRoles(): array
    {
        $roles = [];
        if ($this->role) {
<<<<<<< HEAD
            $roles[] = $this->role->value;
=======
            $role = $this->role;
            if (!str_starts_with($role, 'ROLE_')) {
                $role = 'ROLE_' . $role;
            }
            $roles[] = $role;
>>>>>>> testsisi
        }
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    public function setRoles(array $r): static
    {
<<<<<<< HEAD
        $roleValue = $r[0] ?? 'ROLE_USER';
        $this->role = UserRoleEnum::tryFrom($roleValue);
=======
        $this->role = $r[0] ?? 'ROLE_USER';
>>>>>>> testsisi
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

<<<<<<< HEAD
    public function getRole(): ?UserRoleEnum { return $this->role; }
    public function setRole(?UserRoleEnum $r): static { $this->role = $r; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(#[SensitiveParameter] string $p): static { $this->password = $p; return $this; }
=======
    public function getRole(): ?string { return $this->role; }

    public function getLastActiveAt(): ?\DateTimeInterface { return $this->lastActiveAt; }
    public function setLastActiveAt(?\DateTimeInterface $lastActiveAt): self { $this->lastActiveAt = $lastActiveAt; return $this; }
    
    public function isOnline(): bool
    {
        if (!$this->lastActiveAt) return false;
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $this->lastActiveAt->getTimestamp();
        return $diff < 60; // Online if active in the last minute
    }
    public function setRole(?string $r): static { $this->role = $r; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $p): static { $this->password = $p; return $this; }
>>>>>>> testsisi
    public function eraseCredentials(): void {}
    public function getNom(): string { return $this->nom; }
    public function setNom(string $n): static { $this->nom = $n; return $this; }
    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $p): static { $this->prenom = $p; return $this; }
<<<<<<< HEAD
    public function getFullName(): string { return trim($this->prenom . ' ' . $this->nom); }
=======
>>>>>>> testsisi
    public function getFacebookId(): ?string { return $this->facebookId; }
    public function setFacebookId(?string $f): static { $this->facebookId = $f; return $this; }
    public function getGoogleId(): ?string { return $this->googleId; }
    public function setGoogleId(?string $g): static { $this->googleId = $g; return $this; }
    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $t): static { $this->telephone = $t; return $this; }
    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $a): static { $this->avatar = $a; return $this; }
<<<<<<< HEAD
    public function getCoverPhoto(): ?string { return $this->coverPhoto; }
    public function setCoverPhoto(?string $c): static { $this->coverPhoto = $c; return $this; }
    public function getAuthProvider(): ?string { return $this->authProvider; }
    public function setAuthProvider(?string $a): static { $this->authProvider = $a; return $this; }
    public function getExternalId(): ?string { return $this->externalId; }
    public function setExternalId(?string $e): static { $this->externalId = $e; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getLastSeenAt(): ?\DateTimeInterface { return $this->lastSeenAt; }
    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
    public function getCircuits(): Collection
    {
        return $this->circuits;
    }

    public function addCircuit(Circuit $circuit): self
    {
        if (!$this->circuits->contains($circuit)) {
            $this->circuits->add($circuit);
            $circuit->setCreator($this);
        }
        return $this;
    }

    public function removeCircuit(Circuit $circuit): self
    {
        if ($this->circuits->removeElement($circuit)) {
            if ($circuit->getCreator() === $this) {
                $circuit->setCreator(null);
            }
        }
        return $this;
    }

    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function addStory(Story $story): self
    {
        if (!$this->stories->contains($story)) {
            $this->stories->add($story);
            $story->setUser($this);
        }
        return $this;
    }

    public function removeStory(Story $story): self
    {
        if ($this->stories->removeElement($story)) {
            if ($story->getUser() === $this) {
                $story->setUser(null);
            }
        }
        return $this;
    }

    public function getProfilVoyageur(): ?ProfilVoyageur { return $this->profilVoyageur; }
    public function setProfilVoyageur(?ProfilVoyageur $p): static { $this->profilVoyageur = $p; return $this; }
    public function getResetToken(): ?string { return $this->resetToken; }
    public function setResetToken(#[SensitiveParameter] ?string $t): static { $this->resetToken = $t; return $this; }
    public function getResetTokenExpiresAt(): ?\DateTimeInterface { return $this->resetTokenExpiresAt; }
=======
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $d): static { $this->createdAt = $d; return $this; }
    public function isActive(): bool { return $this->actif; }
    public function getActive(): bool { return $this->actif; }
    public function isActif(): bool { return $this->actif; }
    public function setIsActive(bool $v): static { $this->actif = $v; return $this; }
    public function setActif(bool $a): static { $this->actif = $a; return $this; }
public function getFullName(): string { return trim($this->prenom . ' ' . $this->nom); }
public function getProfilVoyageur(): ?ProfilVoyageur { return $this->profilVoyageur; }
public function setProfilVoyageur(?ProfilVoyageur $p): static { $this->profilVoyageur = $p; return $this; }
public function getResetToken(): ?string { return $this->resetToken; }
public function setResetToken(?string $t): static { $this->resetToken = $t; return $this; }
public function getResetTokenExpiresAt(): ?\DateTimeInterface { return $this->resetTokenExpiresAt; }
public function setResetTokenExpiresAt(?\DateTimeInterface $d): static { $this->resetTokenExpiresAt = $d; return $this; }
>>>>>>> testsisi
}
