<?php
<<<<<<< HEAD
namespace App\Repository;
=======

namespace App\Repository;

>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
use App\Entity\ForumPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForumPostRepository extends ServiceEntityRepository
{
<<<<<<< HEAD
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ForumPost::class); }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['status' => $statut], ['createdAt' => 'DESC']);
    }

    public function search(?string $q, ?string $categorie): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'APPROVED');
        
        if ($q) {
            $qb->andWhere('p.title LIKE :q OR p.content LIKE :q OR p.author LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }
        
        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }
        
        return $qb->orderBy('p.createdAt', 'DESC')->getQuery()->getResult();
    }

    public function searchPaginated(?string $q, ?string $categorie, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'APPROVED')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        
        if ($q) {
            $qb->andWhere('p.title LIKE :q OR p.content LIKE :q OR p.author LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }
        
        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }
        
        return $qb->getQuery()->getResult();
    }

    public function countFiltered(?string $q, ?string $categorie): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'APPROVED');
        
        if ($q) {
            $qb->andWhere('p.title LIKE :q OR p.content LIKE :q OR p.author LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }
        
        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }
        
        return (int) $qb->getQuery()->getSingleScalarResult();
=======
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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    }
}
