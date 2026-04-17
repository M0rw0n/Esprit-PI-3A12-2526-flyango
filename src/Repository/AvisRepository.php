<?php
namespace App\Repository;
use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class AvisRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Avis::class); }
    public function getMoyenneGenerale(): float {
        $result = $this->createQueryBuilder('a')->select('AVG(a.note) as avg')->getQuery()->getSingleScalarResult();
        return round((float)$result, 1);
    }
}
