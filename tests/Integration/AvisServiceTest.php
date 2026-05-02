<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\User;
use App\Service\AvisService;
use App\Service\HebergementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AvisServiceTest extends KernelTestCase
{
    private AvisService $avisService;
    private HebergementService $hebergementService;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->avisService = $container->get(AvisService::class);
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
        $user->setEmail('avis_' . uniqid() . '@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setNom('Test');
        $user->setPrenom('Avis');
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function testCreateAvis(): void
    {
        $hebergement = $this->hebergementService->create('Hotel Avis Test', 'Tunis', 'Hotel', 100.0);
        $user = $this->createUser();

        $avis = $this->avisService->create(
            hebergement: $hebergement,
            user: $user,
            note: 4,
            commentaire: 'Tres bon sejour, je recommande'
        );

        $this->assertNotNull($avis->getId());
        $this->assertEquals(4, $avis->getNote());
        $this->assertEquals('Tres bon sejour, je recommande', $avis->getCommentaire());
        $this->assertSame($hebergement, $avis->getHebergement());
        $this->assertSame($user, $avis->getUser());
    }

    public function testUpdateAndDeleteAvis(): void
    {
        $hebergement = $this->hebergementService->create('Hotel Del Test', 'Sfax', 'Hotel', 75.0);
        $user = $this->createUser();

        $avis = $this->avisService->create($hebergement, $user, 3, 'Moyen');
        $id = $avis->getId();

        $updated = $this->avisService->update($id, 5, 'Excellent sejour!');
        $this->assertNotNull($updated);
        $this->assertEquals(5, $updated->getNote());
        $this->assertEquals('Excellent sejour!', $updated->getCommentaire());

        $deleted = $this->avisService->delete($id);
        $this->assertTrue($deleted);
        $this->assertNull($this->avisService->findById($id));
    }
}
