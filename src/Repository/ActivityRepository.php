<?php
<<<<<<< HEAD
namespace App\Repository;
use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ActivityRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Activity::class); }
    public function search(?string $q, ?string $lieu, ?float $prixMin = null, ?float $prixMax = null, ?string $dateDebut = null, ?string $dateFin = null, ?string $sort = null): array {
        $qb = $this->createQueryBuilder('a');
        if ($q) $qb->andWhere('a.title LIKE :q OR a.description LIKE :q')->setParameter('q','%'.$q.'%');
        if ($lieu) $qb->andWhere('a.lieu = :l')->setParameter('l', $lieu);
        if ($prixMin !== null && $prixMin > 0) $qb->andWhere('a.price >= :pmin')->setParameter('pmin', $prixMin);
        if ($prixMax !== null && $prixMax > 0) $qb->andWhere('a.price <= :pmax')->setParameter('pmax', $prixMax);
        if ($dateDebut) {
            try { $qb->andWhere('a.date >= :dd')->setParameter('dd', new \DateTime($dateDebut)); } catch (\Exception $e) {}
        }
        if ($dateFin) {
            try { $qb->andWhere('a.date <= :df')->setParameter('df', new \DateTime($dateFin)); } catch (\Exception $e) {}
        }
        switch ($sort) {
            case 'prix_asc': $qb->orderBy('a.price', 'ASC'); break;
            case 'prix_desc': $qb->orderBy('a.price', 'DESC'); break;
            case 'date_asc': $qb->orderBy('a.date', 'ASC'); break;
            case 'date_desc': $qb->orderBy('a.date', 'DESC'); break;
            default: $qb->orderBy('a.createdAt', 'DESC');
        }
        return $qb->getQuery()->getResult();
=======

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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    }
}
