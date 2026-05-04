<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TransportOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransportOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportOffer::class);
    }

    public function search(?string $type, ?string $depart, ?string $arrival, ?string $date = null, string $sort = 'date_asc', ?string $q = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.isActive = :active')
            ->setParameter('active', true);

        if ($q) {
            $qb->andWhere('t.companyName LIKE :q OR t.departureCity LIKE :q OR t.arrivalCity LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($type) {
            $qb->andWhere('t.transportType = :type')->setParameter('type', $type);
        }

        if ($depart) {
            $qb->andWhere('t.departureCity LIKE :depart')->setParameter('depart', '%' . $depart . '%');
        }

        if ($arrival) {
            $qb->andWhere('t.arrivalCity LIKE :arrival')->setParameter('arrival', '%' . $arrival . '%');
        }

        match ($sort) {
            'price_asc' => $qb->orderBy('t.price', 'ASC'),
            'price_desc' => $qb->orderBy('t.price', 'DESC'),
            'date_asc' => $qb->orderBy('t.departureDatetime', 'ASC'),
            default => $qb->orderBy('t.departureDatetime', 'DESC'),
        };

<<<<<<< HEAD
        return $qb->setMaxResults(50)->getQuery()->getResult();
=======
        return $qb->getQuery()->getResult();
>>>>>>> testsisi
    }

    public function getDistinctCities(): array
    {
        $departures = $this->createQueryBuilder('t')
            ->select('DISTINCT t.departureCity as city')
            ->where('t.departureCity IS NOT NULL')
            ->getQuery()->getScalarResult();

        $arrivals = $this->createQueryBuilder('t')
            ->select('DISTINCT t.arrivalCity as city')
            ->where('t.arrivalCity IS NOT NULL')
            ->getQuery()->getScalarResult();

        $cities = array_merge(array_column($departures, 'city'), array_column($arrivals, 'city'));
        sort($cities);
        return array_unique($cities);
    }
}
