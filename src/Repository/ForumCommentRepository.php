<?php

namespace App\Repository;

use App\Entity\ForumComment;
use App\Entity\ForumPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForumCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumComment::class);
    }

    public function save(ForumComment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ForumComment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getRootComments(ForumPost $post, string $sort = 'top', int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.post = :post')
            ->andWhere('c.parentId IS NULL')
            ->setParameter('post', $post);

        $qb->orderBy('c.createdAt', 'DESC');

        return $qb->setFirstResult($offset)
                  ->setMaxResults($limit)
                  ->getQuery()
                  ->getResult();
    }

    public function countRootComments(ForumPost $post): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.post = :post')
            ->andWhere('c.parentId IS NULL')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllCommentsForPost(ForumPost $post): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.post = :post')
            ->setParameter('post', $post)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getReplies(int $parentId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.parentId = :parentId')
            ->setParameter('parentId', $parentId)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function buildCommentTree(array $comments): array
    {
        $lookup = [];
        $tree = [];

        foreach ($comments as $comment) {
            $id = $comment->getId();
            $lookup[$id] = [
                'id' => $id,
                'comment' => $comment,
                'replies' => [],
            ];
        }

        foreach ($comments as $comment) {
            $parentId = $comment->getParentId();
            if ($parentId !== null && isset($lookup[$parentId])) {
                $lookup[$parentId]['replies'][] = $lookup[$comment->getId()];
            } else {
                $tree[] = $lookup[$comment->getId()];
            }
        }

        return $tree;
    }
}
