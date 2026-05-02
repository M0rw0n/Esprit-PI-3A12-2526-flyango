<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\User;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;

class AvisService
{
    public function __construct(
        private AvisRepository $repository,
        private EntityManagerInterface $em
    ) {}

    public function create(Hebergement $hebergement, User $user, int $note, string $commentaire): Avis
    {
        $avis = new Avis();
        $avis->setHebergement($hebergement);
        $avis->setUser($user);
        $avis->setNote($note);
        $avis->setCommentaire($commentaire);

        $this->em->persist($avis);
        $this->em->flush();

        return $avis;
    }

    public function findById(int $id): ?Avis
    {
        return $this->repository->find($id);
    }

    public function update(int $id, int $note, string $commentaire): ?Avis
    {
        $avis = $this->findById($id);
        if (!$avis) {
            return null;
        }

        $avis->setNote($note);
        $avis->setCommentaire($commentaire);

        $this->em->flush();

        return $avis;
    }

    public function delete(int $id): bool
    {
        $avis = $this->findById($id);
        if (!$avis) {
            return false;
        }

        $this->em->remove($avis);
        $this->em->flush();

        return true;
    }

    public function findByHebergement(int $hebergementId): array
    {
        return $this->repository->findByHebergement($hebergementId);
    }

    public function getMoyenneGenerale(): float
    {
        return $this->repository->getMoyenneGenerale();
    }
}
