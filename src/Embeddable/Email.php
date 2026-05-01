<?php

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Email
{
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }
}
