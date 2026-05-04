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

<<<<<<< HEAD
    public function getAllCommentsForPost(ForumPost $post, int $limit = 50): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.parentId', 'c.author', 'c.content', 'c.createdAt', 'c.score', 'c.likes', 'c.dislikes', 'c.isPinned', 'c.image')
            ->where('c.post = :post')
            ->setParameter('post', $post)
            ->orderBy('c.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    public function getReplies(int $parentId, int $limit = 50): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'c.parentId', 'c.author', 'c.content', 'c.createdAt', 'c.score', 'c.likes', 'c.dislikes', 'c.isPinned', 'c.image')
            ->where('c.parentId = :parentId')
            ->setParameter('parentId', $parentId)
            ->orderBy('c.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
=======
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
>>>>>>> testsisi
    }

    public function buildCommentTree(array $comments): array
    {
        $lookup = [];
        $tree = [];

        foreach ($comments as $comment) {
<<<<<<< HEAD
            $id = $comment['id'] ?? $comment->getId();
=======
            $id = $comment->getId();
>>>>>>> testsisi
            $lookup[$id] = [
                'id' => $id,
                'comment' => $comment,
                'replies' => [],
            ];
        }

        foreach ($comments as $comment) {
<<<<<<< HEAD
            $parentId = $comment['parentId'] ?? $comment->getParentId();
            if ($parentId !== null && isset($lookup[$parentId])) {
                $lookup[$parentId]['replies'][] = $lookup[$id = $comment['id'] ?? $comment->getId()];
            } else {
                $tree[] = $lookup[$comment['id'] ?? $comment->getId()];
=======
            $parentId = $comment->getParentId();
            if ($parentId !== null && isset($lookup[$parentId])) {
                $lookup[$parentId]['replies'][] = $lookup[$comment->getId()];
            } else {
                $tree[] = $lookup[$comment->getId()];
>>>>>>> testsisi
            }
        }

        return $tree;
    }
}
