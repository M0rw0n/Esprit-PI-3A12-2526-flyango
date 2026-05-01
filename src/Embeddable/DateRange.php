<?php

namespace App\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DateRange
{
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $startDate = null;
    
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $endDate = null;
    
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }
    
    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }
    
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }
    
    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }
}
