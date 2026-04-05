<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdminReviewFilterInput
{
    #[Assert\Length(max: 120)]
    public ?string $q = null;

    #[Assert\Choice(choices: ['Tous', '5', '4', '3', '2', '1'])]
    public string $rating = 'Tous';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->q = self::stringOrNull($request->query->get('q'));
        $dto->rating = self::stringOrNull($request->query->get('rating')) ?? 'Tous';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'rating' => $this->rating,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }
}
