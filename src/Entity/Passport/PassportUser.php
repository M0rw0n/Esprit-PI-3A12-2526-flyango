<?php

namespace App\Entity\Passport;

<<<<<<< HEAD
use App\Repository\Passport\PassportUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Security\Core\Attribute\SensitiveParameter;

#[ORM\Entity(repositoryClass: PassportUserRepository::class)]
=======
use App\Repository\Passport\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
>>>>>>> testsisi
#[ORM\Table(name: 'passport_user')]
class PassportUser implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
<<<<<<< HEAD
    #[Ignore]
=======
>>>>>>> testsisi
    private ?string $password = null;

    #[ORM\Column(name: 'first_name', length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(name: 'points', type: 'integer', options: ['default' => 0])]
    private int $points = 0;

<<<<<<< HEAD
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    /** @var \DateTimeInterface|null Transient, not mapped */
    private ?\DateTimeInterface $updatedAt = null;

    /** @var PassportUser|null Transient, not mapped */
    private ?PassportUser $createdBy = null;

    /** @var PassportUser|null Transient, not mapped */
    private ?PassportUser $updatedBy = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserProgress::class)]
    private $progress;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class)]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: History::class)]
=======
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserProgress::class, cascade: ['remove'])]
    private $progress;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class, cascade: ['remove'])]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class, cascade: ['remove'])]
    private $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: History::class, cascade: ['remove'])]
>>>>>>> testsisi
    private $history;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->progress = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favorites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getUserIdentifier(): string { return (string) $this->email; }
    public function getRoles(): array { return $this->roles; }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }
    public function getPassword(): ?string { return $this->password; }
<<<<<<< HEAD
    public function setPassword(#[SensitiveParameter] string $password): static { $this->password = $password; return $this; }
=======
    public function setPassword(string $password): static { $this->password = $password; return $this; }
>>>>>>> testsisi
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): static { $this->firstName = $firstName; return $this; }
    public function getPoints(): int { return $this->points; }
    public function setPoints(int $points): static { $this->points = $points; return $this; }
    public function addPoints(int $points): static { $this->points += $points; return $this; }
<<<<<<< HEAD
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getCreatedBy(): ?PassportUser { return $this->createdBy; }
    public function getUpdatedBy(): ?PassportUser { return $this->updatedBy; }
=======
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
>>>>>>> testsisi
    public function eraseCredentials(): void {}

    public function getProgress(): \Doctrine\Common\Collections\Collection { return $this->progress; }
    public function getFavorites(): \Doctrine\Common\Collections\Collection { return $this->favorites; }
    public function getReviews(): \Doctrine\Common\Collections\Collection { return $this->reviews; }
    public function getHistory(): \Doctrine\Common\Collections\Collection { return $this->history; }
<<<<<<< HEAD
}
=======
}
>>>>>>> testsisi
