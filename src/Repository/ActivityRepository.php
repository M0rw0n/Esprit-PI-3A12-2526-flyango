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
        
        return $qb->orderBy('a.createdAt', 'DESC')->getQuery()->getResult();
    }

    public function getTopActivitiesByFavorites(int $limit = 6): array
    {
        return $this->createQueryBuilder('a')
            ->select('a, COUNT(f.id) as HIDDEN favCount')
            ->leftJoin('App\Entity\FavoriteActivity', 'f', 'WITH', 'f.activity = a')
            ->andWhere('a.actif = :actif')
            ->setParameter('actif', true)
            ->groupBy('a.id')
            ->orderBy('favCount', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
