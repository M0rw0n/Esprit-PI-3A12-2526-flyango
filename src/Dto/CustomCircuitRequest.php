<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomCircuitRequest
{
    #[Assert\NotBlank(message: 'La destination est obligatoire.')]
    #[Assert\Length(min: 2, max: 150)]
    public ?string $destination = null;

    #[Assert\NotBlank(message: 'La date de départ est obligatoire.')]
    #[Assert\Date]
    public ?string $dateDepart = null;

    #[Assert\Date]
    public ?string $dateRetour = null;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 30)]
    public ?int $duree = 7;

    #[Assert\PositiveOrZero]
    public ?float $budgetMin = 0;

    #[Assert\PositiveOrZero]
    public ?float $budgetMax = 0;

    #[Assert\NotBlank(message: 'Le style de voyage est obligatoire.')]
    #[Assert\Choice(choices: ['Aventure', 'Culture', 'Luxe', 'Détente', 'Économique'])]
    public ?string $styleVoyage = 'Aventure';

    #[Assert\Choice(choices: [1, 2, 3])]
    public int $niveauFatigue = 2;

    #[Assert\Count(min: 1, minMessage: 'Choisissez au moins un centre d’intérêt.')]
    public array $centresInteret = [];

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->destination = self::str($request->request->get('destination'));
        $dto->dateDepart = self::str($request->request->get('date_depart'));
        $dto->dateRetour = self::str($request->request->get('date_retour'));
        $dto->duree = self::int($request->request->get('duree'), 7);
        $dto->budgetMin = self::float($request->request->get('budget_min'), 0);
        $dto->budgetMax = self::float($request->request->get('budget_max'), 0);
        $dto->styleVoyage = self::str($request->request->get('style_voyage')) ?? 'Aventure';
        $dto->niveauFatigue = self::int($request->request->get('niveau_fatigue'), 2);
        $dto->centresInteret = array_values(array_filter(array_map('trim', (array) $request->request->all('centres_interet'))));

        return $dto;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->budgetMax !== null && $this->budgetMin !== null && $this->budgetMax < $this->budgetMin) {
            $context->buildViolation('Le budget maximum doit être supérieur ou égal au budget minimum.')
                ->atPath('budgetMax')
                ->addViolation();
        }

        if ($this->dateDepart && $this->dateRetour && $this->dateRetour < $this->dateDepart) {
            $context->buildViolation('La date de retour doit être postérieure à la date de départ.')
                ->atPath('dateRetour')
                ->addViolation();
        }
    }

    public function toArray(): array
    {
        return [
            'destination' => $this->destination,
            'dateDepart' => $this->dateDepart,
            'dateRetour' => $this->dateRetour,
            'duree' => $this->duree,
            'budgetMin' => $this->budgetMin,
            'budgetMax' => $this->budgetMax,
            'styleVoyage' => $this->styleVoyage,
            'niveauFatigue' => $this->niveauFatigue,
            'centresInteret' => $this->centresInteret,
        ];
    }

    private static function str(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;
        return $value !== '' ? $value : null;
    }

    private static function int(mixed $value, int $default): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }

    private static function float(mixed $value, float $default): float
    {
        return is_numeric($value) ? (float) $value : $default;
    }
}
