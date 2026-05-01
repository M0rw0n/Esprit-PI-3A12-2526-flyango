<?php

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Coordinates
{
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;
    
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;
    
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    
    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }
    
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    
    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }
}
