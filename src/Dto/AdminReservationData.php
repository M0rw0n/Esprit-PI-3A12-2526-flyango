<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdminReservationData
{
    #[Assert\Positive(message: 'Le circuit sélectionné est invalide.')]
    public int $circuitId = 0;

    #[Assert\Positive(message: 'Le client sélectionné est invalide.')]
    public int $userId = 0;

    #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Le nombre de voyageurs doit être compris entre 1 et 12.')]
    public int $nbTravelers = 1;

    #[Assert\Choice(choices: ['CONFIRME', 'EN_ATTENTE', 'ANNULE'])]
    public string $status = 'CONFIRME';

    #[Assert\Date(message: 'La date de départ est invalide.')]
    public ?string $dateDepart = null;

    public ?string $reservedAt = null;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->circuitId = self::intOrDefault($request->request->get('id_circuit'), 0);
        $dto->userId = self::intOrDefault($request->request->get('user_id'), 0);
        $dto->nbTravelers = self::intOrDefault($request->request->get('nb_travelers'), 1);
        $dto->status = self::stringOrNull($request->request->get('status')) ?? 'CONFIRME';
        $dto->dateDepart = self::stringOrNull($request->request->get('date_depart'));
        $dto->reservedAt = self::normalizeDateTime(self::stringOrNull($request->request->get('reserved_at')));

        return $dto;
    }

    public static function fromArray(?array $data): self
    {
        $dto = new self();
        $dto->circuitId = isset($data['id_circuit']) ? (int) $data['id_circuit'] : 0;
        $dto->userId = isset($data['user_id']) ? (int) $data['user_id'] : 0;
        $dto->nbTravelers = isset($data['nb_travelers']) ? (int) $data['nb_travelers'] : 1;
        $dto->status = isset($data['status']) ? (string) $data['status'] : 'CONFIRME';
        $dto->dateDepart = isset($data['date_depart']) && $data['date_depart'] ? (string) $data['date_depart'] : null;
        $dto->reservedAt = self::normalizeDateTime(isset($data['reserved_at']) ? (string) $data['reserved_at'] : null);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'circuitId' => $this->circuitId,
            'userId' => $this->userId,
            'nbTravelers' => $this->nbTravelers,
            'status' => $this->status,
            'dateDepart' => $this->dateDepart,
            'reservedAt' => $this->reservedAt,
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
