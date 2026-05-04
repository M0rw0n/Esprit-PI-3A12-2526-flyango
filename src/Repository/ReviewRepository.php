<?php
namespace App\Repository;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ReviewRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Review::class); }
<<<<<<< HEAD

    public function findByActivityId(int $activityId, int $limit = 50): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.activity = :activityId')
            ->setParameter('activityId', $activityId)
            ->orderBy('r.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countByActivityId(int $activityId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.activity = :activityId')
            ->setParameter('activityId', $activityId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getSentimentSummaryByActivityId(int $activityId): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.sentimentLabel as label, COUNT(r.id) as count')
            ->where('r.activity = :activityId')
            ->andWhere('r.sentimentLabel IS NOT NULL')
            ->setParameter('activityId', $activityId)
            ->groupBy('r.sentimentLabel')
            ->getQuery()
            ->getScalarResult();
    }
=======
>>>>>>> testsisi
}
