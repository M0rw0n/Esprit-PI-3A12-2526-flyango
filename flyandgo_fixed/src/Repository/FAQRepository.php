<?php

namespace App\Repository;

use App\Entity\FAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FAQ>
 */
class FAQRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAQ::class);
    }

    /**
     * @return FAQ[] Returns an array of FAQ objects matching the search query
     */
    public function findByKeyword(string $query): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.question LIKE :query')
            ->orWhere('f.keywords LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
