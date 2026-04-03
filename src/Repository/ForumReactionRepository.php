<?php
namespace App\Repository;
use App\Entity\ForumReaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ForumReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ForumReaction::class); }
    public function findByPost(int $postId): array {
        return $this->findBy(['postId' => $postId]);
    }
    public function countByType(int $postId, string $type): int {
        return (int)$this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.postId = :pid AND r.type = :type')
            ->setParameter('pid', $postId)->setParameter('type', $type)
            ->getQuery()->getSingleScalarResult();
    }
}
