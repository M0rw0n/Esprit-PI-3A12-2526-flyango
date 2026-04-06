<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ReviewRequest
{
    #[Assert\Range(min: 1, max: 5)]
    public int $rating = 5;

    #[Assert\NotBlank(message: 'Le commentaire est obligatoire.')]
    #[Assert\Length(min: 8, max: 1200)]
    public ?string $comment = null;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->rating = is_numeric($request->request->get('rating')) ? (int) $request->request->get('rating') : 5;
        $dto->comment = is_string($request->request->get('comment')) ? trim((string) $request->request->get('comment')) : null;

        return $dto;
    }
}
