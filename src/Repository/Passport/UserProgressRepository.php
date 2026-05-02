<?php

namespace App\Repository\Passport;

use App\Entity\Passport\UserProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProgress::class);
    }

    public function findByUserAndPuzzle(int $userId, int $puzzleId): ?UserProgress
    {
        return $this->findOneBy(['user' => $userId, 'puzzle' => $puzzleId]);
    }

    public function findByUser(int $userId): array
    {
        return $this->findBy(['user' => $userId]);
    }

    public function countCompletedByUser(int $userId): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.user = :userId')
            ->andWhere('p.isCompleted = true')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}