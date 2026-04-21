<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hebergement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HebergementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hebergement::class);
    }

    public function findByVille(string $ville): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.ville = :ville')
            ->setParameter('ville', $ville)
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    public function search(?string $q, ?string $ville, ?string $type, ?float $prixMin, ?float $prixMax, string $sort = 'recent'): array
    {
        $qb = $this->createQueryBuilder('h')
            ->andWhere('h.disponible = :disponible')
            ->setParameter('disponible', true);

        if ($q) {
            $qb->andWhere('h.nom LIKE :q OR h.description LIKE :q OR h.ville LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($ville) {
            $qb->andWhere('h.ville = :ville')->setParameter('ville', $ville);
        }

        if ($type) {
            $qb->andWhere('h.type = :type')->setParameter('type', $type);
        }

        if ($prixMin !== null) {
            $qb->andWhere('h.prixParNuit >= :pmin')->setParameter('pmin', $prixMin);
        }

        if ($prixMax !== null) {
            $qb->andWhere('h.prixParNuit <= :pmax')->setParameter('pmax', $prixMax);
        }

        match ($sort) {
            'price_asc' => $qb->orderBy('h.prixParNuit', 'ASC'),
            'price_desc' => $qb->orderBy('h.prixParNuit', 'DESC'),
            'ville' => $qb->orderBy('h.ville', 'ASC')->addOrderBy('h.nom', 'ASC'),
            default => $qb->orderBy('h.createdAt', 'DESC'),
        };

        return $qb->getQuery()->getResult();
    }

    public function getRevenusParMois(): array
    {
        $totals = [];
        foreach ($this->findAll() as $hebergement) {
            foreach ($hebergement->getReservations() as $reservation) {
                $key = $reservation->getCreatedAt()->format('Y-m');
                if (!isset($totals[$key])) {
                    $totals[$key] = ['mois' => (int) $reservation->getCreatedAt()->format('m'), 'annee' => (int) $reservation->getCreatedAt()->format('Y'), 'total' => 0.0];
                }
                $totals[$key]['total'] += $reservation->getMontantTotal();
            }
        }
        ksort($totals);

        return array_values($totals);
    }

    public function getDistinctVilles(): array
    {
        return $this->createQueryBuilder('h')
            ->select('DISTINCT h.ville')
            ->andWhere('h.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('h.ville', 'ASC')
            ->getQuery()
            ->getScalarResult();
    }
}
