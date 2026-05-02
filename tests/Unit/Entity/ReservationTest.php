<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Hebergement;
use App\Entity\Reservation;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    private Reservation $reservation;

    protected function setUp(): void
    {
        $this->reservation = new Reservation();
    }

    public function testDefaultValues(): void
    {
        $this->assertNull($this->reservation->getId());
        $this->assertEquals('EN_ATTENTE', $this->reservation->getStatut());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->reservation->getCreatedAt());
        $this->assertNull($this->reservation->getFacturePdf());
        $this->assertNull($this->reservation->getQrCode());
        $this->assertNull($this->reservation->getPaymentId());
    }

    public function testSetAndGetHebergement(): void
    {
        $hebergement = new Hebergement();
        $hebergement->setNom('Hotel Tunis');

        $this->reservation->setHebergement($hebergement);
        $this->assertSame($hebergement, $this->reservation->getHebergement());
    }

    public function testSetAndGetUser(): void
    {
        $user = $this->createMock(User::class);

        $this->reservation->setUser($user);
        $this->assertSame($user, $this->reservation->getUser());
    }

    public function testSetAndGetNomClient(): void
    {
        $this->reservation->setNomClient('Ahmed Ben Ali');
        $this->assertEquals('Ahmed Ben Ali', $this->reservation->getNomClient());
    }

    public function testSetAndGetEmailClient(): void
    {
        $this->reservation->setEmailClient('test@example.com');
        $this->assertEquals('test@example.com', $this->reservation->getEmailClient());
    }

    public function testSetAndGetTelephone(): void
    {
        $this->reservation->setTelephone('+216 12 345 678');
        $this->assertEquals('+216 12 345 678', $this->reservation->getTelephone());
    }

    public function testSetAndGetTelephoneClient(): void
    {
        $this->reservation->setTelephoneClient('+216 98 765 432');
        $this->assertEquals('+216 98 765 432', $this->reservation->getTelephoneClient());
    }

    public function testSetAndGetNombrePersonnes(): void
    {
        $this->reservation->setNombrePersonnes(4);
        $this->assertEquals(4, $this->reservation->getNombrePersonnes());
    }

    public function testNombrePersonnesMinimumIs1(): void
    {
        $this->reservation->setNombrePersonnes(0);
        $this->assertEquals(1, $this->reservation->getNombrePersonnes());
    }

    public function testNombrePersonnesNegativeBecomes1(): void
    {
        $this->reservation->setNombrePersonnes(-5);
        $this->assertEquals(1, $this->reservation->getNombrePersonnes());
    }

    public function testSetAndGetNombreChambres(): void
    {
        $this->reservation->setNombreChambres(3);
        $this->assertEquals(3, $this->reservation->getNombreChambres());
    }

    public function testNombreChambresMinimumIs1(): void
    {
        $this->reservation->setNombreChambres(0);
        $this->assertEquals(1, $this->reservation->getNombreChambres());
    }

    public function testSetAndGetDateDebut(): void
    {
        $date = new \DateTime('2026-07-01');
        $this->reservation->setDateDebut($date);
        $this->assertEquals($date, $this->reservation->getDateDebut());
    }

    public function testSetAndGetDateFin(): void
    {
        $date = new \DateTime('2026-07-07');
        $this->reservation->setDateFin($date);
        $this->assertEquals($date, $this->reservation->getDateFin());
    }

    public function testSetAndGetNombreNuits(): void
    {
        $this->reservation->setNombreNuits(5);
        $this->assertEquals(5, $this->reservation->getNombreNuits());
    }

    public function testNombreNuitsMinimumIs1(): void
    {
        $this->reservation->setNombreNuits(0);
        $this->assertEquals(1, $this->reservation->getNombreNuits());
    }

    public function testSetAndGetMontantTotal(): void
    {
        $this->reservation->setMontantTotal(450.75);
        $this->assertEquals(450.75, $this->reservation->getMontantTotal());
    }

    public function testSetAndGetStatut(): void
    {
        $this->reservation->setStatut(Reservation::STATUT_CONFIRMEE);
        $this->assertEquals('CONFIRMEE', $this->reservation->getStatut());
    }

    public function testStatutConstants(): void
    {
        $this->assertEquals('EN_ATTENTE', Reservation::STATUT_EN_ATTENTE);
        $this->assertEquals('CONFIRMEE', Reservation::STATUT_CONFIRMEE);
        $this->assertEquals('ANNULEE', Reservation::STATUT_ANNULEE);
        $this->assertEquals('TERMINEE', Reservation::STATUT_TERMINEE);
    }

    public function testSetAndGetPaymentId(): void
    {
        $this->reservation->setPaymentId('pi_abc123');
        $this->assertEquals('pi_abc123', $this->reservation->getPaymentId());
    }

    public function testSetAndGetPaymentMethod(): void
    {
        $this->reservation->setPaymentMethod('stripe');
        $this->assertEquals('stripe', $this->reservation->getPaymentMethod());
    }

    public function testSetAndGetPaidAt(): void
    {
        $date = new \DateTime('2026-06-15 14:30:00');
        $this->reservation->setPaidAt($date);
        $this->assertEquals($date, $this->reservation->getPaidAt());
    }

    public function testSetAndGetFacturePdf(): void
    {
        $this->reservation->setFacturePdf('factures/facture_123.pdf');
        $this->assertEquals('factures/facture_123.pdf', $this->reservation->getFacturePdf());
    }

    public function testSetAndGetQrCode(): void
    {
        $this->reservation->setQrCode('qr_codes/qr_123.png');
        $this->assertEquals('qr_codes/qr_123.png', $this->reservation->getQrCode());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTime('2026-01-01');
        $this->reservation->setCreatedAt($date);
        $this->assertEquals($date, $this->reservation->getCreatedAt());
    }

    public function testFluentInterface(): void
    {
        $hebergement = new Hebergement();
        $hebergement->setNom('Hotel Sousse');

        $result = $this->reservation
            ->setHebergement($hebergement)
            ->setNomClient('Fatma')
            ->setEmailClient('fatma@test.com')
            ->setNombrePersonnes(2)
            ->setNombreNuits(3)
            ->setMontantTotal(300.0)
            ->setStatut(Reservation::STATUT_CONFIRMEE);

        $this->assertInstanceOf(Reservation::class, $result);
    }

    public function testCalculateMontantFromHebergement(): void
    {
        $hebergement = new Hebergement();
        $hebergement->setPrixParNuit(120.0);

        $this->reservation->setHebergement($hebergement);
        $this->reservation->setNombreNuits(5);
        $this->reservation->setMontantTotal(600.0);

        $this->assertEquals(600.0, $this->reservation->getMontantTotal());
    }

    public function testDefaultDatesAreTodayAndTomorrow(): void
    {
        $today = new \DateTime();
        $tomorrow = new \DateTime('+1 day');

        $this->assertEquals(
            $today->format('Y-m-d'),
            $this->reservation->getDateDebut()->format('Y-m-d')
        );
        $this->assertEquals(
            $tomorrow->format('Y-m-d'),
            $this->reservation->getDateFin()->format('Y-m-d')
        );
    }
}
