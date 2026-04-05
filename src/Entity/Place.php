<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
#[ORM\Table(name: 'place')]
class Place
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(length: 100)]
    private string $country = '';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $n): static { $this->name = $n; return $this; }
    public function getCountry(): string { return $this->country; }
    public function setCountry(string $c): static { $this->country = $c; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $c): static { $this->city = $c; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $i): static { $this->image = $i; return $this; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $l): static { $this->latitude = $l; return $this; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $l): static { $this->longitude = $l; return $this; }
}
