<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationRequest
{
    #[Assert\NotBlank(message: 'La date de départ est obligatoire.')]
    #[Assert\Date]
    public ?string $dateDepart = null;

    #[Assert\Range(min: 1, max: 10)]
    public int $nbTravelers = 2;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->dateDepart = is_string($request->request->get('date_depart')) ? trim((string) $request->request->get('date_depart')) : null;
        $dto->nbTravelers = is_numeric($request->request->get('nb_travelers')) ? (int) $request->request->get('nb_travelers') : 2;

        return $dto;
    }
}
