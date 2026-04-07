<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AdminCircuitData
{
    #[Assert\NotBlank(message: 'Le titre du pack est obligatoire.')]
    #[Assert\Length(min: 3, max: 150)]
    public ?string $title = null;

    #[Assert\NotBlank(message: 'La destination est obligatoire.')]
    #[Assert\Length(min: 2, max: 120)]
    public ?string $destination = null;

    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 20, max: 2000)]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Le type est obligatoire.')]
    #[Assert\Length(min: 2, max: 60)]
    public ?string $type = null;

    #[Assert\Range(min: 1, max: 30, notInRangeMessage: 'La durée doit être comprise entre 1 et 30 jours.')]
    public int $duree = 5;

    #[Assert\Positive(message: 'Le prix par personne doit être supérieur à 0.')]
    public float $prixParPersonne = 0;

    #[Assert\PositiveOrZero(message: 'Le budget doit être positif.')]
    public float $budget = 0;

    #[Assert\Length(max: 500)]
    public ?string $imageUrl = null;

    #[Assert\Date(message: 'La date de départ est invalide.')]
    public ?string $startDate = null;

    #[Assert\Choice(choices: ['actif', 'inactif'])]
    public string $status = 'actif';

    #[Assert\Range(min: 0, max: 100)]
    public int $populariteScore = 50;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->title = self::stringOrNull($request->request->get('title'));
        $dto->destination = self::stringOrNull($request->request->get('destination'));
        $dto->description = self::stringOrNull($request->request->get('description'));
        $dto->type = self::stringOrNull($request->request->get('type'));
        $dto->duree = self::intOrDefault($request->request->get('duree'), 5);
        $dto->prixParPersonne = self::floatOrDefault($request->request->get('prix_par_personne'), 0);
        $dto->budget = self::floatOrDefault($request->request->get('budget'), 0);
        $dto->imageUrl = self::stringOrNull($request->request->get('image_url'));
        $dto->startDate = self::stringOrNull($request->request->get('start_date'));
        $dto->status = self::stringOrNull($request->request->get('status')) ?? 'actif';
        $dto->populariteScore = self::intOrDefault($request->request->get('popularite_score'), 50);

        return $dto;
    }

    public static function fromArray(?array $data): self
    {
        $dto = new self();
        $dto->title = isset($data['title']) ? trim((string) $data['title']) : null;
        $dto->destination = isset($data['destination']) ? trim((string) $data['destination']) : null;
        $dto->description = isset($data['description']) ? trim((string) $data['description']) : null;
        $dto->type = isset($data['type']) ? trim((string) $data['type']) : null;
        $dto->duree = isset($data['duree']) ? (int) $data['duree'] : 5;
        $dto->prixParPersonne = isset($data['prix_par_personne']) ? (float) $data['prix_par_personne'] : 0;
        $dto->budget = isset($data['budget']) ? (float) $data['budget'] : 0;
        $dto->imageUrl = isset($data['image_url']) ? trim((string) $data['image_url']) : null;
        $dto->startDate = isset($data['start_date']) && $data['start_date'] ? (string) $data['start_date'] : null;
        $dto->status = isset($data['status']) ? (string) $data['status'] : 'actif';
        $dto->populariteScore = isset($data['popularite_score']) ? (int) $data['popularite_score'] : 50;

        return $dto;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->budget > 0 && $this->prixParPersonne > $this->budget) {
            $context->buildViolation('Le budget total doit être supérieur ou égal au prix par personne.')
                ->atPath('budget')
                ->addViolation();
        }
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'destination' => $this->destination,
            'description' => $this->description,
            'type' => $this->type,
            'duree' => $this->duree,
            'prixParPersonne' => $this->prixParPersonne,
            'budget' => $this->budget,
            'imageUrl' => $this->imageUrl,
            'startDate' => $this->startDate,
            'status' => $this->status,
            'populariteScore' => $this->populariteScore,
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

    private static function floatOrDefault(mixed $value, float $default): float
    {
        return is_numeric($value) ? (float) $value : $default;
    }
}
