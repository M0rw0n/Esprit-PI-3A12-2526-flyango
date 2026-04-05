<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdminReviewData
{
    #[Assert\Positive(message: 'Le circuit sélectionné est invalide.')]
    public int $circuitId = 0;

    #[Assert\Positive(message: 'Le client sélectionné est invalide.')]
    public int $userId = 0;

    #[Assert\NotBlank(message: 'Le commentaire est obligatoire.')]
    #[Assert\Length(min: 10, max: 1500)]
    public ?string $comment = null;

    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'La note doit être comprise entre 1 et 5.')]
    public int $rating = 5;

    #[Assert\PositiveOrZero(message: 'Le nombre de votes utiles doit être positif.')]
    public int $helpfulCount = 0;

    public bool $verifiedPurchase = true;

    public ?string $createdAt = null;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->circuitId = self::intOrDefault($request->request->get('id_circuit'), 0);
        $dto->userId = self::intOrDefault($request->request->get('user_id'), 0);
        $dto->comment = self::stringOrNull($request->request->get('comment'));
        $dto->rating = self::intOrDefault($request->request->get('rating'), 5);
        $dto->helpfulCount = self::intOrDefault($request->request->get('helpful_count'), 0);
        $dto->verifiedPurchase = (string) $request->request->get('verified_purchase', '1') === '1';
        $dto->createdAt = self::normalizeDateTime(self::stringOrNull($request->request->get('created_at')));

        return $dto;
    }

    public static function fromArray(?array $data): self
    {
        $dto = new self();
        $dto->circuitId = isset($data['id_circuit']) ? (int) $data['id_circuit'] : 0;
        $dto->userId = isset($data['user_id']) ? (int) $data['user_id'] : 0;
        $dto->comment = isset($data['comment']) ? trim((string) $data['comment']) : null;
        $dto->rating = isset($data['rating']) ? (int) $data['rating'] : 5;
        $dto->helpfulCount = isset($data['helpful_count']) ? (int) $data['helpful_count'] : 0;
        $dto->verifiedPurchase = isset($data['verified_purchase']) ? (int) $data['verified_purchase'] === 1 : true;
        $dto->createdAt = self::normalizeDateTime(isset($data['created_at']) ? (string) $data['created_at'] : null);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'circuitId' => $this->circuitId,
            'userId' => $this->userId,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'helpfulCount' => $this->helpfulCount,
            'verifiedPurchase' => $this->verifiedPurchase,
            'createdAt' => $this->createdAt,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    private static function intOrDefault(mixed $value, int $default): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }

    private static function normalizeDateTime(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return str_replace(' ', 'T', substr($value, 0, 16));
    }
}
