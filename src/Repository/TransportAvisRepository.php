<?php

namespace App\Repository;

use App\Entity\TransportAvis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransportAvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportAvis::class);
    }

    public function findByTransportOffer(int $offerId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.transportOffer = :offerId')
            ->setParameter('offerId', $offerId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
