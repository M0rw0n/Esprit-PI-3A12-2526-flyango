<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * Fetch all activities with their Place (JOIN), ordered by id DESC.
     */
    public function findAllWithPlace(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.place', 'p')
            ->addSelect('p')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * AJAX search: filter by title or description (case-insensitive).
     */
    public function searchByTitle(string $q): array
    {
        $term = '%' . strtolower($q) . '%';

        return $this->createQueryBuilder('a')
            ->leftJoin('a.place', 'p')
            ->addSelect('p')
            ->where('LOWER(a.title) LIKE :q OR LOWER(a.description) LIKE :q')
            ->setParameter('q', $term)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find activities for a specific place.
     */
    public function findByPlace(int $placeId): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.place', 'p')
            ->addSelect('p')
            ->where('a.placeId = :pid')
            ->setParameter('pid', $placeId)
            ->orderBy('a.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
