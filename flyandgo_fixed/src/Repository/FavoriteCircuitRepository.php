<?php

namespace App\Repository;

use App\Entity\FavoriteCircuit;
use App\Entity\User;
use App\Entity\Circuit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavoriteCircuitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteCircuit::class);
    }

    public function save(FavoriteCircuit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function remove(FavoriteCircuit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.circuit', 'c')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isFavorited(User $user, Circuit $circuit): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->andWhere('f.user = :user')
            ->andWhere('f.circuit = :circuit')
            ->setParameter('user', $user)
            ->setParameter('circuit', $circuit)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
