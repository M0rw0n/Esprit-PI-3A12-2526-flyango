<?php

namespace App\Repository;

use App\Entity\ProfilVoyageur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfilVoyageurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilVoyageur::class);
    }

    public function findByTypeVoyage(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.typeVoyage = :type')
            ->setParameter('type', $type)
            ->orderBy('p.budget', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByBudgetRange(float $min, float $max): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.budget >= :min AND p.budget <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('p.budget', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByDestination(string $keyword): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.destinationPreferee) LIKE LOWER(:keyword)')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('p.destinationPreferee', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByTypeVoyage(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.typeVoyage, COUNT(p.id) as count')
            ->groupBy('p.typeVoyage')
            ->getQuery()
            ->getResult();
    }

    public function averageBudget(): ?float
    {
        $result = $this->createQueryBuilder('p')
            ->select('AVG(p.budget) as avgBudget')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : null;
    }

    public function topDestinations(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.destinationPreferee, COUNT(p.id) as count')
            ->groupBy('p.destinationPreferee')
            ->orderBy('count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByUser(int $userId): ?ProfilVoyageur
    {
        return $this->createQueryBuilder('p')
<<<<<<< HEAD
            ->join('p.user', 'u')
=======
            ->leftJoin('p.user', 'u')
>>>>>>> testsisi
            ->addSelect('u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getStatistics(): array
    {
        return [
            'countByType' => $this->countByTypeVoyage(),
            'averageBudget' => $this->averageBudget(),
            'topDestinations' => $this->topDestinations(),
            'total' => $this->count([]),
        ];
    }
}
