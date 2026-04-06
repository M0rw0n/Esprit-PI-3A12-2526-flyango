<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationFilterInput
{
    #[Assert\Length(max: 120)]
    public ?string $q = null;

    #[Assert\Length(max: 30)]
    public ?string $status = 'Tous';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->q = is_string($request->query->get('q')) && trim((string) $request->query->get('q')) !== '' ? trim((string) $request->query->get('q')) : null;
        $dto->status = is_string($request->query->get('status')) && trim((string) $request->query->get('status')) !== '' ? trim((string) $request->query->get('status')) : 'Tous';

        return $dto;
    }

    public function toArray(): array
    {
        return ['q' => $this->q, 'status' => $this->status];
    }
}
