<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Avis;
use App\Entity\Hebergement;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AvisTest extends TestCase
{
    private Avis $avis;

    protected function setUp(): void
    {
        $this->avis = new Avis();
    }

    public function testDefaultValues(): void
    {
        $this->assertNull($this->avis->getId());
        $this->assertNull($this->avis->getHebergement());
        $this->assertNull($this->avis->getUser());
        $this->assertNull($this->avis->getNote());
        $this->assertNull($this->avis->getCommentaire());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->avis->getCreatedAt());
        $this->assertNull($this->avis->getSentimentScore());
        $this->assertNull($this->avis->getSentimentLabel());
    }

    public function testSetAndGetNote(): void
    {
        $this->avis->setNote(4);
        $this->assertEquals(4, $this->avis->getNote());
    }

    public function testNoteClampedToMin1(): void
    {
        $this->avis->setNote(0);
        $this->assertEquals(1, $this->avis->getNote());
    }

    public function testNoteClampedToMax5(): void
    {
        $this->avis->setNote(10);
        $this->assertEquals(5, $this->avis->getNote());
    }

    public function testNoteClampedNegativeValue(): void
    {
        $this->avis->setNote(-5);
        $this->assertEquals(1, $this->avis->getNote());
    }

    public function testSetAndGetCommentaire(): void
    {
        $this->avis->setCommentaire('Excellent sejour, tres bon service');
        $this->assertEquals('Excellent sejour, tres bon service', $this->avis->getCommentaire());
    }

    public function testSetAndGetHebergement(): void
    {
        $hebergement = new Hebergement();
        $hebergement->setNom('Hotel Test');

        $this->avis->setHebergement($hebergement);
        $this->assertSame($hebergement, $this->avis->getHebergement());
    }

    public function testSetAndGetUser(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFullName')->willReturn('John Doe');

        $this->avis->setUser($user);
        $this->assertSame($user, $this->avis->getUser());
    }

    public function testGetAuteurWithUser(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFullName')->willReturn('Jane Smith');

        $this->avis->setUser($user);
        $this->assertEquals('Jane Smith', $this->avis->getAuteur());
    }

    public function testGetAuteurWithoutUser(): void
    {
        $this->assertEquals('Anonyme', $this->avis->getAuteur());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTime('2026-05-01');
        $this->avis->setCreatedAt($date);
        $this->assertEquals($date, $this->avis->getCreatedAt());
    }

    public function testSentimentScore(): void
    {
        $this->avis->setSentimentScore(0.85);
        $this->assertEquals(0.85, $this->avis->getSentimentScore());
    }

    public function testSentimentLabel(): void
    {
        $this->avis->setSentimentLabel('positive');
        $this->assertEquals('positive', $this->avis->getSentimentLabel());
    }

    public function testSentimentStars(): void
    {
        $this->avis->setSentimentStars(4);
        $this->assertEquals(4, $this->avis->getSentimentStars());
    }

    public function testSentimentConfidence(): void
    {
        $this->avis->setSentimentConfidence(0.92);
        $this->assertEquals(0.92, $this->avis->getSentimentConfidence());
    }

    public function testSentimentCategory(): void
    {
        $this->avis->setSentimentCategory('Excellent');
        $this->assertEquals('Excellent', $this->avis->getSentimentCategory());
    }

    public function testSentimentSource(): void
    {
        $this->avis->setSentimentSource('huggingface');
        $this->assertEquals('huggingface', $this->avis->getSentimentSource());
    }

    public function testGetSentimentDataDefaults(): void
    {
        $data = $this->avis->getSentimentData();

        $this->assertEquals(0, $data['score']);
        $this->assertEquals('neutral', $data['label']);
        $this->assertEquals(3, $data['stars']);
        $this->assertEquals(0, $data['confidence']);
        $this->assertEquals('Average', $data['category']);
        $this->assertEquals('none', $data['source']);
    }

    public function testGetSentimentDataWithValues(): void
    {
        $this->avis->setSentimentFromAnalysis([
            'score' => 0.75,
            'label' => 'positive',
            'stars' => 4,
            'confidence' => 0.88,
            'category' => 'Good',
            'source' => 'mistral'
        ]);

        $data = $this->avis->getSentimentData();

        $this->assertEquals(0.75, $data['score']);
        $this->assertEquals('positive', $data['label']);
        $this->assertEquals(4, $data['stars']);
        $this->assertEquals(0.88, $data['confidence']);
        $this->assertEquals('Good', $data['category']);
        $this->assertEquals('mistral', $data['source']);
    }

    public function testSetSentimentFromAnalysis(): void
    {
        $analysis = [
            'score' => -0.5,
            'label' => 'negative',
            'stars' => 2,
            'confidence' => 0.76,
            'category' => 'Bad',
            'source' => 'local'
        ];

        $result = $this->avis->setSentimentFromAnalysis($analysis);

        $this->assertInstanceOf(Avis::class, $result);
        $this->assertEquals(-0.5, $this->avis->getSentimentScore());
        $this->assertEquals('negative', $this->avis->getSentimentLabel());
        $this->assertEquals(2, $this->avis->getSentimentStars());
        $this->assertEquals(0.76, $this->avis->getSentimentConfidence());
        $this->assertEquals('Bad', $this->avis->getSentimentCategory());
        $this->assertEquals('local', $this->avis->getSentimentSource());
    }

    public function testSetSentimentFromAnalysisPartialData(): void
    {
        $this->avis->setSentimentFromAnalysis(['score' => 0.3, 'label' => 'positive']);

        $this->assertEquals(0.3, $this->avis->getSentimentScore());
        $this->assertEquals('positive', $this->avis->getSentimentLabel());
        $this->assertEquals(3, $this->avis->getSentimentStars()); // default
        $this->assertEquals('Average', $this->avis->getSentimentCategory()); // default
    }

    public function testFluentInterface(): void
    {
        $hebergement = new Hebergement();
        $hebergement->setNom('Test Hotel');

        $result = $this->avis
            ->setNote(5)
            ->setCommentaire('Superbe experience')
            ->setHebergement($hebergement)
            ->setSentimentScore(0.9);

        $this->assertInstanceOf(Avis::class, $result);
    }
}
