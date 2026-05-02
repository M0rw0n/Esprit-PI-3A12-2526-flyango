<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Hebergement;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;

class HebergementService
{
    public function __construct(
        private HebergementRepository $repository,
        private EntityManagerInterface $em
    ) {}

    public function create(string $nom, string $ville, string $type, float $prixParNuit, ?string $description = null): Hebergement
    {
        $hebergement = new Hebergement();
        $hebergement->setNom($nom);
        $hebergement->setVille($ville);
        $hebergement->setType($type);
        $hebergement->setPrixParNuit($prixParNuit);
        $hebergement->setDescription($description);
        $hebergement->setDisponible(true);

        $this->em->persist($hebergement);
        $this->em->flush();

        return $hebergement;
    }

    public function findById(int $id): ?Hebergement
    {
        return $this->repository->find($id);
    }

    public function update(int $id, array $data): ?Hebergement
    {
        $hebergement = $this->findById($id);
        if (!$hebergement) {
            return null;
        }

        if (isset($data['nom'])) {
            $hebergement->setNom($data['nom']);
        }
        if (isset($data['ville'])) {
            $hebergement->setVille($data['ville']);
        }
        if (isset($data['type'])) {
            $hebergement->setType($data['type']);
        }
        if (isset($data['prixParNuit'])) {
            $hebergement->setPrixParNuit($data['prixParNuit']);
        }
        if (isset($data['description'])) {
            $hebergement->setDescription($data['description']);
        }
        if (isset($data['disponible'])) {
            $hebergement->setDisponible($data['disponible']);
        }

        $this->em->flush();

        return $hebergement;
    }

    public function delete(int $id): bool
    {
        $hebergement = $this->findById($id);
        if (!$hebergement) {
            return false;
        }

        $this->em->remove($hebergement);
        $this->em->flush();

        return true;
    }

    public function findByVille(string $ville): array
    {
        return $this->repository->findByVille($ville);
    }

    public function findAllDisponible(): array
    {
        return $this->repository->findBy(['disponible' => true]);
    }
}
