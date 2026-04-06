<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ComparatorInput
{
    #[Assert\NotBlank(message: 'La destination est obligatoire.')]
    #[Assert\Length(min: 2, max: 120)]
    public ?string $destination = null;

    #[Assert\Date]
    public ?string $depart = null;

    #[Assert\Date]
    public ?string $retour = null;

    #[Assert\Range(min: 1, max: 10)]
    public int $voyageurs = 2;

    #[Assert\Choice(choices: ['Tous', 'Transport', 'Hôtels', 'Circuits', 'Activités'])]
    public string $categorie = 'Tous';

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->destination = is_string($request->query->get('destination')) ? trim((string) $request->query->get('destination')) : null;
        $dto->depart = is_string($request->query->get('depart')) ? trim((string) $request->query->get('depart')) : null;
        $dto->retour = is_string($request->query->get('retour')) ? trim((string) $request->query->get('retour')) : null;
        $dto->voyageurs = is_numeric($request->query->get('voyageurs')) ? (int) $request->query->get('voyageurs') : 2;
        $dto->categorie = is_string($request->query->get('categorie')) && trim((string) $request->query->get('categorie')) !== '' ? trim((string) $request->query->get('categorie')) : 'Tous';

        return $dto;
    }
}
