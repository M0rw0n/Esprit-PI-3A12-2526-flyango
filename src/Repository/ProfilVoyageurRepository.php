<?php

namespace App\Repository;

use App\Entity\ProfilVoyageur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfilVoyageur>
 */
class ProfilVoyageurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilVoyageur::class);
    }

    /**
     * Find profils with filters
     */
    public function findByFilters(
        ?string $typeVoyage = null,
        ?string $destination = null,
        ?float $budgetMin = null,
        ?float $budgetMax = null
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        if ($typeVoyage && $typeVoyage !== 'all') {
            $qb->andWhere('p.typeVoyage = :typeVoyage')
                ->setParameter('typeVoyage', $typeVoyage);
        }

        if ($destination) {
            $qb->andWhere('p.destinationPreferee LIKE :destination')
                ->setParameter('destination', "%{$destination}%");
        }

        if ($budgetMin !== null) {
            $qb->andWhere('p.budget >= :budgetMin')
                ->setParameter('budgetMin', $budgetMin);
        }

        if ($budgetMax !== null) {
            $qb->andWhere('p.budget <= :budgetMax')
                ->setParameter('budgetMax', $budgetMax);
        }

        return $qb->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get statistics by travel type
     */
    public function getStatsByType(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.typeVoyage, COUNT(p.user) as count')
            ->groupBy('p.typeVoyage')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
