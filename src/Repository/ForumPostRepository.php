<?php
namespace App\Repository;
use App\Entity\ForumPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ForumPost::class); }

    public function findByStatut(string $statut): array
    {
        return $this->findBy(['status' => $statut], ['createdAt' => 'DESC']);
    }

    public function search(?string $q, ?string $categorie, int $limit = 50): array
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

        return $qb->orderBy('p.createdAt', 'DESC')->setMaxResults($limit)->getQuery()->getResult();
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
    }

    public function findCommentCountsByPostIds(array $ids): array
    {
        if (empty($ids)) return [];
        
        $result = $this->createQueryBuilder('p')
            ->select('p.id, COUNT(c.id) as commentCount')
            ->leftJoin('p.comments', 'c')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('p.id')
            ->getQuery()
            ->getScalarResult();
            
        $counts = [];
        foreach ($result as $row) {
            $counts[(int) $row['id']] = (int) $row['commentCount'];
        }
        return $counts;
    }
}
