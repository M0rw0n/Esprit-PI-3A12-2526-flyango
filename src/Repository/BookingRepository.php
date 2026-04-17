<?php
namespace App\Repository;
use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
<<<<<<< HEAD
class BookingRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Booking::class); }
=======
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Booking::class); }

    public function findByActivity(int $activityId): array {
        return $this->findBy(['activityId' => $activityId], ['id' => 'DESC']);
    }

    public function countByStatus(string $status): int {
        return (int)$this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.status = :status')->setParameter('status', $status)
            ->getQuery()->getSingleScalarResult();
    }
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
}
