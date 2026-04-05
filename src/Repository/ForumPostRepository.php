<?php

namespace App\Repository;

use App\Entity\ForumPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPost::class);
    }

    /**
     * Fetch all posts with eagerly loaded comments + reactions, ordered newest first.
     */
    public function findAllWithCommentsAndReactions(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments',  'c')
            ->leftJoin('p.reactions', 'r')
            ->addSelect('c', 'r')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * AJAX search posts by title or content (case-insensitive).
     */
    public function searchPosts(string $q): array
    {
        $term = '%' . strtolower($q) . '%';

        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments',  'c')
            ->leftJoin('p.reactions', 'r')
            ->addSelect('c', 'r')
            ->where('LOWER(p.title) LIKE :q OR LOWER(p.content) LIKE :q OR LOWER(p.author) LIKE :q')
            ->setParameter('q', $term)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Only approved posts (for homepage).
     */
    public function findApproved(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->where('p.status = :status')
            ->setParameter('status', 'APPROVED')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
