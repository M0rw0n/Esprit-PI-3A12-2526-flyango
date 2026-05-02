<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function getRevenusParMois(): array
    {
        $totals = [];
        foreach ($this->findBy([], ['createdAt' => 'ASC']) as $reservation) {
            $key = $reservation->getCreatedAt()->format('Y-m');
            if (!isset($totals[$key])) {
                $totals[$key] = [
                    'mois' => (int) $reservation->getCreatedAt()->format('m'),
                    'annee' => (int) $reservation->getCreatedAt()->format('Y'),
                    'total' => 0.0,
                ];
            }
            $totals[$key]['total'] += $reservation->getMontantTotal();
        }

        return array_values($totals);
    }

    public function getTauxOccupationParVille(): array
    {
        $totals = [];
        foreach ($this->findAll() as $reservation) {
            $ville = $reservation->getHebergement()?->getVille() ?? 'N/A';
            $totals[$ville] = ($totals[$ville] ?? 0) + 1;
        }
        arsort($totals);

        return array_map(
            static fn(string $ville, int $total): array => ['ville' => $ville, 'total' => $total],
            array_keys($totals),
            array_values($totals)
        );
    }

    public function getTopHebergements(): array
    {
        $rows = [];
        foreach ($this->findAll() as $reservation) {
            $hebergement = $reservation->getHebergement();
            if (!$hebergement) {
                continue;
            }

            $key = (string) $hebergement->getId();
            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'nom' => $hebergement->getNom(),
                    'ville' => $hebergement->getVille(),
                    'nbRes' => 0,
                    'revenus' => 0.0,
                ];
            }

            $rows[$key]['nbRes']++;
            $rows[$key]['revenus'] += $reservation->getMontantTotal();
        }

        usort($rows, static fn(array $a, array $b): int => $b['revenus'] <=> $a['revenus']);

        return array_slice($rows, 0, 10);
    }
}
