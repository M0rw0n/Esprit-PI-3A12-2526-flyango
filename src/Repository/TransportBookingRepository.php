<?php

namespace App\Repository;

use App\Entity\TransportBooking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransportBookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportBooking::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.bookingDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->andWhere('b.status != :cancelled')
            ->andWhere('b.pickupDatetime >= :now')
            ->setParameter('user', $user)
            ->setParameter('cancelled', 'CANCELLED')
            ->setParameter('now', new \DateTime())
            ->orderBy('b.pickupDatetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getRevenusParMois(): array
    {
<<<<<<< HEAD
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT MONTH(b.booking_date) as mois, YEAR(b.booking_date) as annee, SUM(b.total_price) as total 
                FROM transport_booking b 
                WHERE b.status != :cancelled
                GROUP BY annee, mois 
                ORDER BY annee ASC, mois ASC';
        
        return $conn->executeQuery($sql, ['cancelled' => 'CANCELLED'])->fetchAllAssociative();
=======
        $totals = [];
        foreach ($this->findAll() as $booking) {
            if ($booking->getStatus() !== 'CANCELLED') {
                $key = $booking->getBookingDate()->format('Y-m');
                if (!isset($totals[$key])) {
                    $totals[$key] = [
                        'mois' => (int) $booking->getBookingDate()->format('m'),
                        'annee' => (int) $booking->getBookingDate()->format('Y'),
                        'total' => 0.0,
                    ];
                }
                $totals[$key]['total'] += $booking->getTotalPrice();
            }
        }
        ksort($totals);
        return array_values($totals);
>>>>>>> testsisi
    }
}
