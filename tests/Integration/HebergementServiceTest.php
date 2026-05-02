<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Hebergement;
use App\Entity\User;
use App\Service\HebergementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HebergementServiceTest extends KernelTestCase
{
    private HebergementService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->service = $container->get(HebergementService::class);
        $this->em = $container->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->em->close();
        parent::tearDown();
    }

    public function testCreateHebergement(): void
    {
        $hebergement = $this->service->create(
            nom: 'Hotel Sidi Bou Said',
            ville: 'Tunis',
            type: 'Hotel',
            prixParNuit: 120.0,
            description: 'Hotel vue mer'
        );

        $this->assertNotNull($hebergement->getId());
        $this->assertEquals('Hotel Sidi Bou Said', $hebergement->getNom());
        $this->assertEquals('Tunis', $hebergement->getVille());
        $this->assertEquals('Hotel', $hebergement->getType());
        $this->assertEquals(120.0, $hebergement->getPrixParNuit());
        $this->assertEquals('Hotel vue mer', $hebergement->getDescription());
        $this->assertTrue($hebergement->isDisponible());
    }

    public function testUpdateAndDeleteHebergement(): void
    {
        $hebergement = $this->service->create(
            nom: 'Hotel Test',
            ville: 'Sousse',
            type: 'Appartement',
            prixParNuit: 80.0
        );

        $id = $hebergement->getId();

        $updated = $this->service->update($id, [
            'nom' => 'Hotel Updated',
            'prixParNuit' => 95.0,
            'disponible' => false
        ]);

        $this->assertNotNull($updated);
        $this->assertEquals('Hotel Updated', $updated->getNom());
        $this->assertEquals(95.0, $updated->getPrixParNuit());
        $this->assertFalse($updated->isDisponible());

        $deleted = $this->service->delete($id);
        $this->assertTrue($deleted);
        $this->assertNull($this->service->findById($id));
    }
}
