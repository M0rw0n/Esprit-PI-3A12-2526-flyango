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
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT MONTH(r.created_at) as mois, YEAR(r.created_at) as annee, SUM(r.montant_total) as total 
                FROM reservation r 
                GROUP BY annee, mois 
                ORDER BY annee ASC, mois ASC';
        
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function getTauxOccupationParVille(): array
    {
        return $this->createQueryBuilder('r')
            ->select('h.ville as ville')
            ->addSelect('COUNT(r.id) as total')
            ->join('r.hebergement', 'h')
            ->groupBy('h.ville')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getScalarResult();
    }

    public function getTopHebergements(): array
    {
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
    }
}
