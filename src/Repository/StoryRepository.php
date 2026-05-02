<?php

namespace App\Repository;

use App\Entity\Story;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    /**
     * Get active stories (not expired) from friends and self
     */
    public function findActiveFeed(User $user): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('s')
            ->andWhere('s.expiresAt > :now')
            ->setParameter('now', $now)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Delete expired stories
     */
    public function deleteExpired(): int
    {
        $now = new \DateTime();
        
        return $this->getEntityManager()->createQuery(
            'DELETE FROM App\Entity\Story s WHERE s.expiresAt < :now'
        )
        ->setParameter('now', $now)
        ->execute();
    }
}
