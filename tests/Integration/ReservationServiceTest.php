<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Reservation;
use App\Entity\User;
use App\Service\HebergementService;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReservationServiceTest extends KernelTestCase
{
    private ReservationService $service;
    private HebergementService $hebergementService;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->service = $container->get(ReservationService::class);
        $this->hebergementService = $container->get(HebergementService::class);
        $this->em = $container->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->em->close();
        parent::tearDown();
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail('reserv_' . uniqid() . '@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setNom('Reserv');
        $user->setPrenom('Test');
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function testCreateReservation(): void
    {
        $hebergement = $this->hebergementService->create('Hotel Reserv Test', 'Hammamet', 'Hotel', 150.0);
        $user = $this->createUser();

        $dateDebut = new \DateTime('2026-08-01');
        $dateFin = new \DateTime('2026-08-06');

        $reservation = $this->service->create(
            hebergement: $hebergement,
            user: $user,
            nomClient: 'John Doe',
            emailClient: 'john_' . uniqid() . '@example.com',
            dateDebut: $dateDebut,
            dateFin: $dateFin,
            nombrePersonnes: 2,
            montantTotal: 750.0
        );

        $this->assertNotNull($reservation->getId());
        $this->assertEquals('John Doe', $reservation->getNomClient());
        $this->assertEquals(2, $reservation->getNombrePersonnes());
        $this->assertEquals(750.0, $reservation->getMontantTotal());
        $this->assertEquals(5, $reservation->getNombreNuits());
        $this->assertEquals(Reservation::STATUT_EN_ATTENTE, $reservation->getStatut());
    }

    public function testUpdateStatutAndDeleteReservation(): void
    {
        $hebergement = $this->hebergementService->create('Hotel Del Reserv', 'Djerba', 'Resort', 200.0);
        $user = $this->createUser();

        $dateDebut = new \DateTime('2026-09-10');
        $dateFin = new \DateTime('2026-09-15');

        $reservation = $this->service->create(
            hebergement: $hebergement,
            user: $user,
            nomClient: 'Jane Smith',
            emailClient: 'jane_' . uniqid() . '@example.com',
            dateDebut: $dateDebut,
            dateFin: $dateFin,
            nombrePersonnes: 1,
            montantTotal: 1000.0
        );

        $id = $reservation->getId();

        $updated = $this->service->updateStatut($id, Reservation::STATUT_CONFIRMEE);
        $this->assertNotNull($updated);
        $this->assertEquals(Reservation::STATUT_CONFIRMEE, $updated->getStatut());

        $deleted = $this->service->delete($id);
        $this->assertTrue($deleted);
        $this->assertNull($this->service->findById($id));
    }
}
