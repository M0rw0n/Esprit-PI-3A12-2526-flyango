<?php

namespace App\Entity\Passport;

use App\Repository\Passport\PuzzleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleRepository::class)]
#[ORM\Table(name: 'passport_puzzle')]
class Puzzle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private string $title = '';

    #[ORM\Column(name: 'city_name', length: 100)]
    private string $cityName = '';

    #[ORM\Column(name: 'country_name', length: 100)]
    private string $countryName = '';

    #[ORM\Column(type: 'text')]
    private string $clue = '';

    #[ORM\Column(name: 'image_filename', length: 255, nullable: true)]
    private ?string $imageFilename = null;

    #[ORM\Column(length: 20)]
    private string $difficulty = 'medium';

    #[ORM\Column(name: 'order_index', type: 'integer')]
    private int $orderIndex = 0;

    #[ORM\OneToMany(mappedBy: 'puzzle', targetEntity: UserProgress::class)]
    private $userProgress;

    #[ORM\OneToMany(mappedBy: 'puzzle', targetEntity: Favorite::class)]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'puzzle', targetEntity: Review::class)]
    private $reviews;

    #[ORM\OneToMany(mappedBy: 'puzzle', targetEntity: History::class)]
    private $history;

    public function __construct()
    {
        $this->userProgress = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favorites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getCityName(): string { return $this->cityName; }
    public function setCityName(string $cityName): static { $this->cityName = $cityName; return $this; }
    public function getCountryName(): string { return $this->countryName; }
    public function setCountryName(string $countryName): static { $this->countryName = $countryName; return $this; }
    public function getClue(): string { return $this->clue; }
    public function setClue(string $clue): static { $this->clue = $clue; return $this; }
    public function getImageFilename(): ?string { return $this->imageFilename; }
    public function setImageFilename(?string $imageFilename): static { $this->imageFilename = $imageFilename; return $this; }
    public function getDifficulty(): string { return $this->difficulty; }
    public function setDifficulty(string $difficulty): static { $this->difficulty = $difficulty; return $this; }
    public function getOrderIndex(): int { return $this->orderIndex; }
    public function setOrderIndex(int $orderIndex): static { $this->orderIndex = $orderIndex; return $this; }

    public function getUserProgress(): \Doctrine\Common\Collections\Collection { return $this->userProgress; }
    public function getFavorites(): \Doctrine\Common\Collections\Collection { return $this->favorites; }
    public function getReviews(): \Doctrine\Common\Collections\Collection { return $this->reviews; }
    public function getHistory(): \Doctrine\Common\Collections\Collection { return $this->history; }
}