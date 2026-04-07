<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CircuitSearchInput
{
    #[Assert\Length(max: 120)]
    public ?string $q = null;

    #[Assert\Length(max: 50)]
    public ?string $type = 'Tous';

    #[Assert\PositiveOrZero]
    public ?float $maxPrice = null;

    #[Assert\Positive]
    public ?int $maxDuration = null;

    #[Assert\Choice(choices: ['popularite', 'prix_asc', 'prix_desc', 'note_desc', 'duree_asc'])]
    public string $sort = 'popularite';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->q = self::stringOrNull($request->query->get('q'));
        $dto->type = self::stringOrNull($request->query->get('type')) ?? 'Tous';
        $dto->maxPrice = self::floatOrNull($request->query->get('maxPrice'));
        $dto->maxDuration = self::intOrNull($request->query->get('maxDuration'));
        $dto->sort = self::stringOrNull($request->query->get('sort')) ?? 'popularite';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'type' => $this->type,
            'maxPrice' => $this->maxPrice,
            'maxDuration' => $this->maxDuration,
            'sort' => $this->sort,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    private static function floatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private static function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
