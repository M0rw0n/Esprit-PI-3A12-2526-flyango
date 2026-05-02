<?php

namespace App\Repository;

use App\Entity\FavoriteHebergement;
use App\Entity\Hebergement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavoriteHebergementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteHebergement::class);
    }

    public function save(FavoriteHebergement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavoriteHebergement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.hebergement', 'h')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isFavorited(User $user, Hebergement $hebergement): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->andWhere('f.user = :user')
            ->andWhere('f.hebergement = :hebergement')
            ->setParameter('user', $user)
            ->setParameter('hebergement', $hebergement)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopHebergements(int $limit = 10): array
    {
        return $this->createQueryBuilder('f')
            ->select('h, COUNT(f.id) as favoriteCount, AVG(a.note) as avgRating')
            ->join('f.hebergement', 'h')
            ->leftJoin('h.avis', 'a')
            ->groupBy('h.id')
            ->orderBy('favoriteCount', 'DESC')
            ->addOrderBy('avgRating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
