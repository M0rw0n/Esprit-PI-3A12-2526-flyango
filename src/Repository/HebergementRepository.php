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

    public function findByVille(string $ville, int $limit = 50): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.ville = :ville')
            ->setParameter('ville', $ville)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type, int $limit = 50): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.type = :type')
            ->setParameter('type', $type)
            ->setMaxResults($limit)
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

        return $qb->setMaxResults(50)->getQuery()->getResult();
    }

    public function getRevenusParMois(): array
    {
        return $this->createQueryBuilder('h')
            ->select('SUBSTRING(r.createdAt, 1, 7) as periode')
            ->addSelect('SUM(r.montantTotal) as total')
            ->join('h.reservations', 'r')
            ->groupBy('periode')
            ->orderBy('periode', 'ASC')
            ->getQuery()
            ->getScalarResult();
    }

    public function getDistinctVilles(): array
    {
        return $this->createQueryBuilder('h')
            ->select('DISTINCT h.ville')
            ->andWhere('h.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('h.ville', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAvisCountsByHebergementIds(array $hebergementIds): array
    {
        if (empty($hebergementIds)) {
            return [];
        }

        return $this->createQueryBuilder('h')
            ->select('h.id as hebergement_id')
            ->addSelect('COUNT(a.id) as nb_avis')
            ->leftJoin('h.avis', 'a')
            ->andWhere('h.id IN (:ids)')
            ->setParameter('ids', $hebergementIds)
            ->groupBy('h.id')
            ->getQuery()
            ->getScalarResult();
    }
}
