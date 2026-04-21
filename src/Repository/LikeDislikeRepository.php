<?php

namespace App\Repository;

use App\Entity\LikeDislike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LikeDislikeRepository extends ServiceEntityRepository
{
    public const TYPE_COMMENT = 'comment';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeDislike::class);
    }

    public function save(LikeDislike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LikeDislike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserVote(User $user, string $targetType, int $targetId): ?int
    {
        $vote = $this->createQueryBuilder('l')
            ->select('l.vote')
            ->andWhere('l.user = :user')
            ->andWhere('l.targetType = :type')
            ->andWhere('l.targetId = :id')
            ->setParameter('user', $user)
            ->setParameter('type', $targetType)
            ->setParameter('id', $targetId)
            ->getQuery()
            ->getOneOrNullResult();

        return $vote ? (int) $vote['vote'] : null;
    }

    public function getCounts(string $targetType, array $targetIds): array
    {
        if (empty($targetIds)) return [];

        $results = $this->createQueryBuilder('l')
            ->select('l.targetId, SUM(l.vote) as score, SUM(CASE WHEN l.vote = 1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN l.vote = -1 THEN 1 ELSE 0 END) as dislikes')
            ->andWhere('l.targetType = :type')
            ->andWhere('l.targetId IN (:ids)')
            ->setParameter('type', $targetType)
            ->setParameter('ids', $targetIds)
            ->groupBy('l.targetId')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($results as $r) {
            $counts[$r['targetId']] = [
                'score' => (int) $r['score'],
                'likes' => (int) $r['likes'],
                'dislikes' => (int) $r['dislikes'],
            ];
        }
        return $counts;
    }

    public function getCount(string $targetType, int $targetId): array
    {
        $result = $this->createQueryBuilder('l')
            ->select('SUM(l.vote) as score, SUM(CASE WHEN l.vote = 1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN l.vote = -1 THEN 1 ELSE 0 END) as dislikes')
            ->andWhere('l.targetType = :type')
            ->andWhere('l.targetId = :id')
            ->setParameter('type', $targetType)
            ->setParameter('id', $targetId)
            ->getQuery()
            ->getSingleResult();

        return [
            'score' => (int) ($result['score'] ?? 0),
            'likes' => (int) ($result['likes'] ?? 0),
            'dislikes' => (int) ($result['dislikes'] ?? 0),
        ];
    }

    public function getVotesForComments(User $user, array $commentIds): array
    {
        if (empty($commentIds)) return [];

        return $this->createQueryBuilder('l')
            ->select('l.targetId, l.vote')
            ->andWhere('l.user = :user')
            ->andWhere('l.targetType = :type')
            ->andWhere('l.targetId IN (:ids)')
            ->setParameter('user', $user)
            ->setParameter('type', LikeDislike::TYPE_COMMENT)
            ->setParameter('ids', $commentIds)
            ->getQuery()
            ->getResult();
    }
}
