<?php

namespace App\Repository;

use App\Entity\FavoriteTransport;
use App\Entity\User;
use App\Entity\TransportOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavoriteTransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteTransport::class);
    }

    public function save(FavoriteTransport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function remove(FavoriteTransport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) $this->getEntityManager()->flush();
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.transport', 't')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isFavorited(User $user, TransportOffer $transport): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->andWhere('f.user = :user')
            ->andWhere('f.transport = :transport')
            ->setParameter('user', $user)
            ->setParameter('transport', $transport)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
