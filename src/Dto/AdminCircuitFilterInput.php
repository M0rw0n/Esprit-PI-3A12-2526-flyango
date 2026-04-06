<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdminCircuitFilterInput
{
    #[Assert\Length(max: 120)]
    public ?string $q = null;

    #[Assert\Length(max: 60)]
    public string $type = 'Tous';

    #[Assert\Choice(choices: ['Tous', 'actif', 'inactif'])]
    public string $status = 'Tous';

    #[Assert\Choice(choices: ['recent', 'prix_asc', 'prix_desc', 'note_desc', 'popularite'])]
    public string $sort = 'recent';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->q = self::stringOrNull($request->query->get('q'));
        $dto->type = self::stringOrNull($request->query->get('type')) ?? 'Tous';
        $dto->status = self::stringOrNull($request->query->get('status')) ?? 'Tous';
        $dto->sort = self::stringOrNull($request->query->get('sort')) ?? 'recent';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'type' => $this->type,
            'status' => $this->status,
            'sort' => $this->sort,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }
}
