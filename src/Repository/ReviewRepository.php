<?php
namespace App\Repository;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Review::class); }

    public function findByActivity(int $activityId): array {
        return $this->createQueryBuilder('r')
            ->where('r.activityId = :id')->setParameter('id', $activityId)
            ->orderBy('r.createdAt','DESC')->getQuery()->getResult();
    }

    public function getAverageRating(int $activityId): float {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as avg')
            ->where('r.activityId = :id')->setParameter('id', $activityId)
            ->getQuery()->getSingleScalarResult();
        return round((float)$result, 1);
    }
}
