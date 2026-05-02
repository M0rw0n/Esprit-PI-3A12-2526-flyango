<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\Reservation;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class HebergementTest extends TestCase
{
    private Hebergement $hebergement;

    protected function setUp(): void
    {
        $this->hebergement = new Hebergement();
    }

    public function testDefaultValues(): void
    {
        $this->assertNull($this->hebergement->getId());
        $this->assertTrue($this->hebergement->isDisponible());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->hebergement->getCreatedAt());
        $this->assertEmpty($this->hebergement->getBlockedDates());
        $this->assertEmpty($this->hebergement->getGaleriePhotos());
        $this->assertEmpty($this->hebergement->getEquipements());
        $this->assertEmpty($this->hebergement->getPhotos360());
        $this->assertEmpty($this->hebergement->getReservations());
        $this->assertEmpty($this->hebergement->getAvis());
        $this->assertEquals(0, $this->hebergement->getVues());
    }

    public function testSetAndGetNom(): void
    {
        $this->hebergement->setNom('Hotel Paris');
        $this->assertEquals('Hotel Paris', $this->hebergement->getNom());
    }

    public function testSetAndGetVille(): void
    {
        $this->hebergement->setVille('Tunis');
        $this->assertEquals('Tunis', $this->hebergement->getVille());
    }

    public function testSetAndGetType(): void
    {
        $this->hebergement->setType('Hotel');
        $this->assertEquals('Hotel', $this->hebergement->getType());
    }

    public function testSetAndGetPrixParNuit(): void
    {
        $this->hebergement->setPrixParNuit(150.5);
        $this->assertEquals(150.5, $this->hebergement->getPrixParNuit());
    }

    public function testSetAndGetImage(): void
    {
        $this->hebergement->setImage('uploads/hotel.jpg');
        $this->assertEquals('uploads/hotel.jpg', $this->hebergement->getImage());
    }

    public function testSetAndGetDescription(): void
    {
        $this->hebergement->setDescription('Un bel hotel');
        $this->assertEquals('Un bel hotel', $this->hebergement->getDescription());
    }

    public function testSetAndGetAdresse(): void
    {
        $this->hebergement->setAdresse('123 Rue Habib');
        $this->assertEquals('123 Rue Habib', $this->hebergement->getAdresse());
    }

    public function testSetAndGetCapacite(): void
    {
        $this->hebergement->setCapacite(10);
        $this->assertEquals(10, $this->hebergement->getCapacite());
    }

    public function testSetAndGetDisponible(): void
    {
        $this->hebergement->setDisponible(false);
        $this->assertFalse($this->hebergement->isDisponible());
    }

    public function testSetAndGetUserId(): void
    {
        $this->hebergement->setUserId(42);
        $this->assertEquals(42, $this->hebergement->getUserId());
    }

    public function testSetAndGetCoordinates(): void
    {
        $this->hebergement->setLatitude(36.8065);
        $this->hebergement->setLongitude(10.1815);
        $this->hebergement->setLocalisation('Tunis, Tunisia');

        $this->assertEquals(36.8065, $this->hebergement->getLatitude());
        $this->assertEquals(10.1815, $this->hebergement->getLongitude());
        $this->assertEquals('Tunis, Tunisia', $this->hebergement->getLocalisation());
    }

    public function testBlockedDates(): void
    {
        $this->hebergement->addBlockedDate('2026-06-01');
        $this->hebergement->addBlockedDate('2026-06-02');
        $this->hebergement->addBlockedDate('2026-06-01'); // Duplicate

        $dates = $this->hebergement->getBlockedDates();
        $this->assertCount(2, $dates);
        $this->assertContains('2026-06-01', $dates);
        $this->assertContains('2026-06-02', $dates);
    }

    public function testSetBlockedDates(): void
    {
        $dates = ['2026-07-01', '2026-07-02'];
        $this->hebergement->setBlockedDates($dates);
        $this->assertEquals($dates, $this->hebergement->getBlockedDates());
    }

    public function testGaleriePhotos(): void
    {
        $this->hebergement->addGaleriePhoto('photo1.jpg');
        $this->hebergement->addGaleriePhoto('photo2.jpg');

        $photos = $this->hebergement->getGaleriePhotos();
        $this->assertCount(2, $photos);
        $this->assertEquals('photo1.jpg', $photos[0]);
        $this->assertEquals('photo2.jpg', $photos[1]);
    }

    public function testSetGaleriePhotos(): void
    {
        $photos = ['a.jpg', 'b.jpg', 'c.jpg'];
        $this->hebergement->setGaleriePhotos($photos);
        $this->assertEquals($photos, $this->hebergement->getGaleriePhotos());
    }

    public function testPhotos360(): void
    {
        $this->hebergement->addPhoto360('360_1.jpg');
        $this->hebergement->addPhoto360('360_2.jpg');
        $this->hebergement->removePhoto360('360_1.jpg');

        $photos = $this->hebergement->getPhotos360();
        $this->assertCount(1, $photos);
        $this->assertContains('360_2.jpg', $photos);
    }

    public function testSetPhotos360(): void
    {
        $photos = ['360_a.jpg', '360_b.jpg'];
        $this->hebergement->setPhotos360($photos);
        $this->assertEquals($photos, $this->hebergement->getPhotos360());
    }

    public function testIncrementVues(): void
    {
        $this->assertEquals(0, $this->hebergement->getVues());
        $this->hebergement->incrementVues();
        $this->assertEquals(1, $this->hebergement->getVues());
        $this->hebergement->incrementVues();
        $this->assertEquals(2, $this->hebergement->getVues());
    }

    public function testSetVues(): void
    {
        $this->hebergement->setVues(100);
        $this->assertEquals(100, $this->hebergement->getVues());
    }

    public function testEquipements(): void
    {
        $equipements = ['wifi', 'parking', 'pool'];
        $this->hebergement->setEquipements($equipements);
        $this->assertEquals($equipements, $this->hebergement->getEquipements());
    }

    public function testAmadeusId(): void
    {
        $this->hebergement->setAmadeusId('AMA_12345');
        $this->assertEquals('AMA_12345', $this->hebergement->getAmadeusId());
    }

    public function testNote(): void
    {
        $this->hebergement->setNote(4.5);
        $this->assertEquals(4.5, $this->hebergement->getNote());
    }

    public function testChambresDisponibles(): void
    {
        $this->hebergement->setChambresDisponibles(5);
        $this->assertEquals(5, $this->hebergement->getChambresDisponibles());
    }

    public function testMoyenneNotesWithNoAvis(): void
    {
        $this->assertEquals(0, $this->hebergement->getMoyenneNotes());
    }

    public function testMoyenneNotesWithAvis(): void
    {
        $avis1 = (new Avis())->setNote(4);
        $avis2 = (new Avis())->setNote(5);
        $avis3 = (new Avis())->setNote(3);

        $this->hebergement->getAvis()->add($avis1);
        $this->hebergement->getAvis()->add($avis2);
        $this->hebergement->getAvis()->add($avis3);

        $this->assertEquals(4.0, $this->hebergement->getMoyenneNotes());
    }

    public function testCapaciteRestante(): void
    {
        $this->hebergement->setCapacite(20);

        $today = new \DateTime();
        $future = new \DateTime('+10 days');
        $past = new \DateTime('-10 days');

        $reservationActive = $this->createMockReservation(5, 'CONFIRMEE', $future);
        $reservationPast = $this->createMockReservation(3, 'CONFIRMEE', $past);
        $reservationPending = $this->createMockReservation(2, 'EN_ATTENTE', $future);

        $this->hebergement->getReservations()->add($reservationActive);
        $this->hebergement->getReservations()->add($reservationPast);
        $this->hebergement->getReservations()->add($reservationPending);

        $this->assertEquals(15, $this->hebergement->getCapaciteRestante());
    }

    public function testCapaciteRestanteNoCapacity(): void
    {
        $this->hebergement->setCapacite(5);

        $future = new \DateTime('+5 days');
        $reservation = $this->createMockReservation(10, 'CONFIRMEE', $future);
        $this->hebergement->getReservations()->add($reservation);

        $this->assertEquals(0, $this->hebergement->getCapaciteRestante());
    }

    public function testSetCreatedAt(): void
    {
        $date = new \DateTime('2026-01-15');
        $this->hebergement->setCreatedAt($date);
        $this->assertEquals($date, $this->hebergement->getCreatedAt());
    }

    public function testFluentInterface(): void
    {
        $result = $this->hebergement
            ->setNom('Test')
            ->setVille('Sousse')
            ->setType('Appartement')
            ->setPrixParNuit(80.0);

        $this->assertInstanceOf(Hebergement::class, $result);
    }

    private function createMockReservation(int $personnes, string $statut, \DateTimeInterface $dateFin): Reservation
    {
        $reservation = $this->createMock(Reservation::class);
        $reservation->method('getNombrePersonnes')->willReturn($personnes);
        $reservation->method('getStatut')->willReturn($statut);
        $reservation->method('getDateFin')->willReturn($dateFin);

        return $reservation;
    }
}
