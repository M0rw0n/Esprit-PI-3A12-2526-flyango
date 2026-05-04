<?php

namespace App\Repository;

use App\Entity\PromoCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

<<<<<<< HEAD
=======
/**
 * @extends ServiceEntityRepository<PromoCode>
 */
>>>>>>> testsisi
class PromoCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoCode::class);
    }
<<<<<<< HEAD
}
=======

    public function save(PromoCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PromoCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCode(string $code): ?PromoCode
    {
        return $this->findOneBy(['code' => strtoupper($code)]);
    }

    public function findValidByCode(string $code): ?PromoCode
    {
        $promo = $this->findByCode($code);
        return $promo?->isValid() ? $promo : null;
    }
}
>>>>>>> testsisi
