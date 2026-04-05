<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 20)]
    private string $role = 'VOYAGEUR';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $profilePicturePath = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $coverPhotoPath = null;

    #[ORM\Column(length: 20)]
    private string $authProvider = 'LOCAL';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['remove'])]
    private ?ProfilVoyageur $profilVoyageur = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PasswordResetToken::class, cascade: ['remove'])]
    private Collection $passwordResetTokens;

    public function __construct()
    {
        $this->passwordResetTokens = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNomComplet(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function getInitiales(): string
    {
        $initNom = substr($this->nom ?? '', 0, 1);
        $initPrenom = substr($this->prenom ?? '', 0, 1);
        return strtoupper("{$initPrenom}{$initNom}");
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    public function getProfilePicturePath(): ?string
    {
        return $this->profilePicturePath;
    }

    public function setProfilePicturePath(?string $profilePicturePath): static
    {
        $this->profilePicturePath = $profilePicturePath;
        return $this;
    }

    public function getCoverPhotoPath(): ?string
    {
        return $this->coverPhotoPath;
    }

    public function setCoverPhotoPath(?string $coverPhotoPath): static
    {
        $this->coverPhotoPath = $coverPhotoPath;
        return $this;
    }

    public function getAuthProvider(): ?string
    {
        return $this->authProvider;
    }

    public function setAuthProvider(string $authProvider): static
    {
        $this->authProvider = $authProvider;
        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        // convert DateTime to DateTimeImmutable to keep immutability where possible
        if (!$createdAt instanceof \DateTimeImmutable) {
            $createdAt = \DateTimeImmutable::createFromMutable($createdAt);
        }

        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProfilVoyageur(): ?ProfilVoyageur
    {
        return $this->profilVoyageur;
    }

    public function setProfilVoyageur(?ProfilVoyageur $profilVoyageur): static
    {
        if ($profilVoyageur === null && $this->profilVoyageur !== null) {
            $this->profilVoyageur->setUser(null);
        } else if ($profilVoyageur !== null && $this->profilVoyageur !== $profilVoyageur) {
            $profilVoyageur->setUser($this);
        }

        $this->profilVoyageur = $profilVoyageur;
        return $this;
    }

    /**
     * @return Collection<int, PasswordResetToken>
     */
    public function getPasswordResetTokens(): Collection
    {
        return $this->passwordResetTokens;
    }

    public function addPasswordResetToken(PasswordResetToken $passwordResetToken): static
    {
        if (!$this->passwordResetTokens->contains($passwordResetToken)) {
            $this->passwordResetTokens->add($passwordResetToken);
            $passwordResetToken->setUser($this);
        }

        return $this;
    }

    public function removePasswordResetToken(PasswordResetToken $passwordResetToken): static
    {
        if ($this->passwordResetTokens->removeElement($passwordResetToken)) {
            if ($passwordResetToken->getUser() === $this) {
                $passwordResetToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        if ($this->role === 'ADMIN') {
            $roles[] = 'ROLE_ADMIN';
        } else {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
