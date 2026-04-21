<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function getMoyenneGenerale(): float
    {
        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.note)')
            ->getQuery()
            ->getSingleScalarResult();
        return round((float)$result, 1);
    }

    public function findByHebergement(int $hebergementId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithSentimentByHebergement(int $hebergementId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->andWhere('a.sentimentScore IS NOT NULL')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAverageSentimentByHebergement(int $hebergementId): ?array
    {
        $result = $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->andWhere('a.sentimentScore IS NOT NULL')
            ->select('AVG(a.sentimentScore) as avgScore', 'AVG(a.sentimentStars) as avgStars', 'COUNT(a.id) as total')
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    public function findPositiveReviews(int $hebergementId, float $minScore = 0.25): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->andWhere('a.sentimentScore >= :minScore')
            ->setParameter('minScore', $minScore)
            ->orderBy('a.sentimentScore', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findNegativeReviews(int $hebergementId, float $maxScore = -0.25): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->andWhere('a.sentimentScore <= :maxScore')
            ->setParameter('maxScore', $maxScore)
            ->orderBy('a.sentimentScore', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getSentimentDistributionByHebergement(int $hebergementId): array
    {
        $reviews = $this->createQueryBuilder('a')
            ->andWhere('a.hebergement = :hebergementId')
            ->setParameter('hebergementId', $hebergementId)
            ->andWhere('a.sentimentLabel IS NOT NULL')
            ->getQuery()
            ->getResult();

        $distribution = [
            'excellent' => 0,
            'good' => 0,
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0,
            'bad' => 0
        ];

        foreach ($reviews as $review) {
            $label = $review->getSentimentLabel();
            if ($label && isset($distribution[$label])) {
                $distribution[$label]++;
            }
        }

        return $distribution;
    }

    public function findUnanalyzedReviews(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.commentaire IS NOT NULL')
            ->andWhere('a.commentaire != :empty')
            ->setParameter('empty', '')
            ->andWhere('a.sentimentScore IS NULL')
            ->getQuery()
            ->getResult();
    }
}
