<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdminReservationFilterInput
{
    #[Assert\Length(max: 120)]
    public ?string $q = null;

    #[Assert\Length(max: 30)]
    public string $status = 'Tous';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->q = self::stringOrNull($request->query->get('q'));
        $dto->status = self::stringOrNull($request->query->get('status')) ?? 'Tous';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'status' => $this->status,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }
}
