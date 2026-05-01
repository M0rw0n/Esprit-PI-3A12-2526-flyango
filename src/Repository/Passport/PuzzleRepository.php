<?php

namespace App\Repository\Passport;

use App\Entity\Passport\Puzzle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PuzzleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Puzzle::class);
    }

    public function findAllOrdered(): array
    {
        return $this->findBy([], ['orderIndex' => 'ASC']);
    }

    public function findById(int $id): ?Puzzle
    {
        return $this->find($id);
    }
}