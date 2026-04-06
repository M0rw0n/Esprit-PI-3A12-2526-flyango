<?php
namespace App\Repository;
use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Place::class); }
    public function findAllOrderedByName(): array {
        return $this->createQueryBuilder('p')->orderBy('p.name','ASC')->getQuery()->getResult();
    }
}
