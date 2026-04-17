<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Circuit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CircuitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circuit::class);
    }

    public function searchVisible(?string $q, ?string $difficulte, string $sort = 'recent', ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.actif = :actif')
            ->setParameter('actif', true);

        if ($user) {
            $qb->andWhere('(c.sourceType = :adminSource OR c.creator = :currentUser)')
               ->setParameter('adminSource', 'admin')
               ->setParameter('currentUser', $user);
        } else {
            $qb->andWhere('c.sourceType = :adminSource')
               ->setParameter('adminSource', 'admin');
        }

        if ($q) {
            $qb->andWhere('c.titre LIKE :q OR c.description LIKE :q OR c.destination LIKE :q OR c.depart LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($difficulte) {
            $qb->andWhere('c.difficulte = :difficulte')->setParameter('difficulte', $difficulte);
        }

        match ($sort) {
            'price_asc' => $qb->orderBy('c.prix', 'ASC'),
            'price_desc' => $qb->orderBy('c.prix', 'DESC'),
            'places_desc' => $qb->orderBy('c.placesDisponibles', 'DESC'),
            default => $qb->orderBy('c.createdAt', 'DESC'),
        };

        return $qb->getQuery()->getResult();
    }

    public function findUserCustomCircuits(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.creator = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSimilar(Circuit $circuit, int $limit = 4): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id != :id')
            ->andWhere('c.actif = :actif')
            ->andWhere('c.sourceType = :source')
            ->setParameter('id', $circuit->getId())
            ->setParameter('actif', true)
            ->setParameter('source', 'admin')
            ->andWhere('(c.destination LIKE :dest OR c.type LIKE :type)')
            ->setParameter('dest', '%' . $circuit->getDestination() . '%')
            ->setParameter('type', '%' . ($circuit->getType() ?? '') . '%')
            ->orderBy('c.prix', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findWithPromo(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.actif = :actif')
            ->andWhere('c.sourceType = :source')
            ->andWhere('c.promoPrix IS NOT NULL')
            ->setParameter('actif', true)
            ->setParameter('source', 'admin')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
