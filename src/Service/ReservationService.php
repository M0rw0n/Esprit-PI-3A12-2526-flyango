<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    public function __construct(
        private ReservationRepository $repository,
        private EntityManagerInterface $em
    ) {}

    public function create(
        Hebergement $hebergement,
        User $user,
        string $nomClient,
        string $emailClient,
        \DateTimeInterface $dateDebut,
        \DateTimeInterface $dateFin,
        int $nombrePersonnes,
        float $montantTotal
    ): Reservation {
        $nombreNuits = $dateDebut->diff($dateFin)->days;

        $reservation = new Reservation();
        $reservation->setHebergement($hebergement);
        $reservation->setUser($user);
        $reservation->setNomClient($nomClient);
        $reservation->setEmailClient($emailClient);
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setNombrePersonnes($nombrePersonnes);
        $reservation->setNombreNuits($nombreNuits);
        $reservation->setMontantTotal($montantTotal);
        $reservation->setStatut(Reservation::STATUT_EN_ATTENTE);

        $this->em->persist($reservation);
        $this->em->flush();

        return $reservation;
    }

    public function findById(int $id): ?Reservation
    {
        return $this->repository->find($id);
    }

    public function updateStatut(int $id, string $statut): ?Reservation
    {
        $reservation = $this->findById($id);
        if (!$reservation) {
            return null;
        }

        $reservation->setStatut($statut);
        $this->em->flush();

        return $reservation;
    }

    public function delete(int $id): bool
    {
        $reservation = $this->findById($id);
        if (!$reservation) {
            return false;
        }

        $this->em->remove($reservation);
        $this->em->flush();

        return true;
    }

    public function findByUser(User $user): array
    {
        return $this->repository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    public function getRevenusParMois(): array
    {
        return $this->repository->getRevenusParMois();
    }
}
