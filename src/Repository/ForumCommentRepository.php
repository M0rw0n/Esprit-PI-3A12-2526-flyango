<?php
namespace App\Repository;
use App\Entity\ForumComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ForumCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ForumComment::class); }
    public function findByPost(int $postId): array {
        return $this->findBy(['postId' => $postId], ['createdAt' => 'ASC']);
    }
}
