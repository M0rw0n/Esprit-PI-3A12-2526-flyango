<?php
namespace App\Repository;
use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ActivityRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Activity::class); }
    public function search(?string $q, ?string $lieu, ?string $categorie = null, ?float $prixMax = null): array {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.actif = :actif')
            ->setParameter('actif', true);
        if ($q) $qb->andWhere('a.title LIKE :q OR a.description LIKE :q')->setParameter('q','%'.$q.'%');
        if ($lieu) $qb->andWhere('a.lieu LIKE :lieu')->setParameter('lieu','%'.$lieu.'%');
        if ($categorie) $qb->andWhere('a.title LIKE :cat OR a.description LIKE :cat')->setParameter('cat','%'.$categorie.'%');
        if ($prixMax) $qb->andWhere('a.price <= :pm')->setParameter('pm', $prixMax);
        return $qb->orderBy('a.createdAt','DESC')->getQuery()->getResult();
    }
}
