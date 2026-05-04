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
<<<<<<< HEAD
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT MONTH(r.created_at) as mois, YEAR(r.created_at) as annee, SUM(r.montant_total) as total 
                FROM reservation r 
                GROUP BY annee, mois 
                ORDER BY annee ASC, mois ASC';
        
        return $conn->executeQuery($sql)->fetchAllAssociative();
=======
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
>>>>>>> testsisi
    }

    public function getTauxOccupationParVille(): array
    {
<<<<<<< HEAD
        return $this->createQueryBuilder('r')
            ->select('h.ville as ville')
            ->addSelect('COUNT(r.id) as total')
            ->join('r.hebergement', 'h')
            ->groupBy('h.ville')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getScalarResult();
=======
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
>>>>>>> testsisi
    }

    public function getTopHebergements(): array
    {
<<<<<<< HEAD
        return $this->createQueryBuilder('r')
            ->select('h.nom as nom')
            ->addSelect('h.ville as ville')
            ->addSelect('COUNT(r.id) as nbRes')
            ->addSelect('SUM(r.montantTotal) as revenus')
            ->join('r.hebergement', 'h')
            ->groupBy('h.id')
            ->orderBy('revenus', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getScalarResult();
=======
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
>>>>>>> testsisi
    }
}
