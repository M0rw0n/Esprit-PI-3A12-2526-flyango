<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FlyAndGoFunctionalTest extends WebTestCase
{
    private string $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectDir = dirname(__DIR__);
        $this->resetDatabase();
    }

    public function testUserHomeAndAdminDashboardRenderReferenceSections(): void
    {
        $client = static::createClient();

        $client->request('GET', '/espace-user?user=2');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Trouvez votre prochain voyage');
        self::assertStringContainsString('Score voyageur', $client->getResponse()->getContent());
        self::assertStringContainsString('Bronze', $client->getResponse()->getContent());

        $client->request('GET', '/admin/dashboard');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Dashboard');
        self::assertStringContainsString('FLY &amp; GO', $client->getResponse()->getContent());
        self::assertStringContainsString('Vol, taxi, circuits &amp; séjours · admin', $client->getResponse()->getContent());
        self::assertStringContainsString('Total Projects', $client->getResponse()->getContent());
        self::assertStringContainsString('Time Tracker', $client->getResponse()->getContent());
    }

    public function testAdminCircuitCrudAndValidationFlow(): void
    {
        $client = static::createClient();
        $connection = static::getContainer()->get(Connection::class);

        $client->request('GET', '/admin/circuits');
        self::assertResponseIsSuccessful();

        $client->submitForm('Ajouter le pack', [
            'title' => '',
            'destination' => '',
            'type' => '',
            'description' => 'trop court',
            'duree' => 0,
            'prix_par_personne' => 3000,
            'budget' => 1200,
            'start_date' => '2026-05-10',
            'status' => 'actif',
            'popularite_score' => 120,
            'image_url' => 'not-a-url',
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertStringContainsString('Le titre du pack est obligatoire', $client->getResponse()->getContent());
        self::assertStringContainsString('Le budget total doit être supérieur ou égal au prix par personne', $client->getResponse()->getContent());

        $initialCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit');

        $client->request('GET', '/admin/circuits');
        $client->submitForm('Ajouter le pack', [
            'title' => 'Pack test CRUD',
            'destination' => 'Tozeur, Tunisie',
            'type' => 'Aventure',
            'description' => 'Pack de test complet pour vérifier ajout, modification, suppression et contrôle de saisie.',
            'duree' => 5,
            'prix_par_personne' => 980,
            'budget' => 1960,
            'start_date' => '2026-06-18',
            'status' => 'actif',
            'popularite_score' => 72,
            'image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
        ]);
        self::assertResponseRedirects('/admin/circuits');
        $client->followRedirect();
        self::assertStringContainsString('Nouveau pack ajouté avec succès', $client->getResponse()->getContent());
        self::assertSame($initialCount + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit'));

        $circuitId = (int) $connection->fetchOne('SELECT id_circuit FROM circuit WHERE title = :title', ['title' => 'Pack test CRUD']);
        self::assertGreaterThan(0, $circuitId);

        $client->request('GET', '/admin/circuits?edit=' . $circuitId);
        $client->submitForm('Mettre à jour le pack', [
            'title' => 'Pack test CRUD modifié',
            'destination' => 'Djerba, Tunisie',
            'type' => 'Détente',
            'description' => 'Version modifiée du pack de test pour valider la mise à jour complète du CRUD.',
            'duree' => 6,
            'prix_par_personne' => 1050,
            'budget' => 2100,
            'start_date' => '2026-06-25',
            'status' => 'inactif',
            'popularite_score' => 55,
            'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
        ]);
        self::assertResponseRedirects('/admin/circuits?edit=' . $circuitId);
        $client->followRedirect();
        self::assertStringContainsString('Pack mis à jour avec succès', $client->getResponse()->getContent());
        self::assertSame('Pack test CRUD modifié', (string) $connection->fetchOne('SELECT title FROM circuit WHERE id_circuit = :id', ['id' => $circuitId]));
        self::assertSame('inactif', (string) $connection->fetchOne('SELECT status FROM circuit WHERE id_circuit = :id', ['id' => $circuitId]));

        $crawler = $client->request('GET', '/admin/circuits');
        $deleteToken = $crawler->filter(sprintf('form[action="/admin/circuits/%d/delete"] input[name="_token"]', $circuitId))->attr('value');

        $client->request('POST', '/admin/circuits/' . $circuitId . '/delete', [
            '_token' => $deleteToken,
        ]);
        self::assertResponseRedirects('/admin/circuits');
        self::assertSame(0, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit WHERE id_circuit = :id', ['id' => $circuitId]));
    }

    public function testAdminReservationAndReviewCrudFlows(): void
    {
        $client = static::createClient();
        $connection = static::getContainer()->get(Connection::class);

        $initialReservations = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation');
        $client->request('GET', '/admin/reservations');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('CRUD complet des réservations', $client->getResponse()->getContent());

        $client->submitForm('Ajouter la réservation', [
            'id_circuit' => 3,
            'user_id' => 3,
            'nb_travelers' => 2,
            'date_depart' => '2026-07-12',
            'status' => 'EN_ATTENTE',
            'reserved_at' => '2026-04-01T10:15',
        ]);
        self::assertResponseRedirects('/admin/reservations');
        $client->followRedirect();
        self::assertStringContainsString('Réservation ajoutée avec succès', $client->getResponse()->getContent());
        self::assertSame($initialReservations + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation'));

        $reservationId = (int) $connection->fetchOne(
            'SELECT id FROM circuit_reservation WHERE user_id = :user AND id_circuit = :circuit AND reserved_at = :reservedAt',
            ['user' => 3, 'circuit' => 3, 'reservedAt' => '2026-04-01 10:15:00']
        );
        self::assertGreaterThan(0, $reservationId);
        self::assertSame('EN_ATTENTE', (string) $connection->fetchOne('SELECT status FROM circuit_reservation WHERE id = :id', ['id' => $reservationId]));

        $client->request('GET', '/admin/reservations?edit=' . $reservationId);
        $client->submitForm('Mettre à jour la réservation', [
            'id_circuit' => 3,
            'user_id' => 4,
            'nb_travelers' => 4,
            'date_depart' => '2026-07-16',
            'status' => 'CONFIRME',
            'reserved_at' => '2026-04-01T11:30',
        ]);
        self::assertResponseRedirects('/admin/reservations?edit=' . $reservationId);
        $client->followRedirect();
        self::assertStringContainsString('Réservation mise à jour avec succès', $client->getResponse()->getContent());
        self::assertSame('CONFIRME', (string) $connection->fetchOne('SELECT status FROM circuit_reservation WHERE id = :id', ['id' => $reservationId]));
        self::assertSame(4, (int) $connection->fetchOne('SELECT nb_travelers FROM circuit_reservation WHERE id = :id', ['id' => $reservationId]));
        self::assertSame('2026-04-01 11:30:00', (string) $connection->fetchOne('SELECT reserved_at FROM circuit_reservation WHERE id = :id', ['id' => $reservationId]));

        $crawler = $client->request('GET', '/admin/reservations');
        $deleteToken = $crawler->filter(sprintf('form[action="/admin/reservations/%d/delete"] input[name="_token"]', $reservationId))->attr('value');
        $client->request('POST', '/admin/reservations/' . $reservationId . '/delete', [
            '_token' => $deleteToken,
        ]);
        self::assertResponseRedirects('/admin/reservations');
        self::assertSame(0, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation WHERE id = :id', ['id' => $reservationId]));

        $initialReviews = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_review');
        $client->request('GET', '/admin/avis');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('CRUD complet des avis', $client->getResponse()->getContent());

        $client->submitForm('Ajouter un avis', [
            'id_circuit' => 3,
            'user_id' => 4,
            'rating' => 4,
            'helpful_count' => 2,
            'verified_purchase' => '1',
            'created_at' => '2026-04-01T09:30',
            'comment' => 'Avis de test assez détaillé pour valider la création d’un feedback admin.',
        ]);
        self::assertResponseRedirects('/admin/avis');
        $client->followRedirect();
        self::assertStringContainsString('Avis ajouté avec succès', $client->getResponse()->getContent());
        self::assertSame($initialReviews + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_review'));

        $reviewId = (int) $connection->fetchOne(
            'SELECT id FROM circuit_review WHERE user_id = :user AND id_circuit = :circuit AND created_at = :createdAt',
            ['user' => 4, 'circuit' => 3, 'createdAt' => '2026-04-01 09:30:00']
        );
        self::assertGreaterThan(0, $reviewId);
        self::assertSame(1, (int) $connection->fetchOne('SELECT nb_avis FROM circuit WHERE id_circuit = 3'));

        $client->request('GET', '/admin/avis?edit=' . $reviewId);
        $client->submitForm('Mettre à jour un avis', [
            'id_circuit' => 3,
            'user_id' => 5,
            'rating' => 2,
            'helpful_count' => 5,
            'verified_purchase' => '0',
            'created_at' => '2026-04-01T12:45',
            'comment' => 'Avis modifié pour tester la mise à jour admin et le recalcul des statistiques.',
        ]);
        self::assertResponseRedirects('/admin/avis?edit=' . $reviewId);
        $client->followRedirect();
        self::assertStringContainsString('Avis mis à jour avec succès', $client->getResponse()->getContent());
        self::assertSame(2, (int) $connection->fetchOne('SELECT rating FROM circuit_review WHERE id = :id', ['id' => $reviewId]));
        self::assertSame(5, (int) $connection->fetchOne('SELECT helpful_count FROM circuit_review WHERE id = :id', ['id' => $reviewId]));
        self::assertSame('2026-04-01 12:45:00', (string) $connection->fetchOne('SELECT created_at FROM circuit_review WHERE id = :id', ['id' => $reviewId]));

        $crawler = $client->request('GET', '/admin/avis');
        $deleteToken = $crawler->filter(sprintf('form[action="/admin/avis/%d/delete"] input[name="_token"]', $reviewId))->attr('value');
        $client->request('POST', '/admin/avis/' . $reviewId . '/delete', [
            '_token' => $deleteToken,
        ]);
        self::assertResponseRedirects('/admin/avis');
        self::assertSame(0, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_review WHERE id = :id', ['id' => $reviewId]));
        self::assertSame(0, (int) $connection->fetchOne('SELECT nb_avis FROM circuit WHERE id_circuit = 3'));
    }

    public function testReservationReviewAndCustomCircuitBusinessFlows(): void
    {
        $client = static::createClient();
        $connection = static::getContainer()->get(Connection::class);

        $initialReservations = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation');
        $initialReviews = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_review');
        $initialCustom = (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_personnalise');

        $client->request('GET', '/circuits/1?user=2');
        self::assertResponseIsSuccessful();
        $client->submitForm('Confirmer la réservation', [
            'date_depart' => '2026-06-14',
            'nb_travelers' => 2,
        ]);
        self::assertResponseRedirects('/mes-reservations?user=2');
        $client->followRedirect();
        self::assertStringContainsString('Réservation', $client->getResponse()->getContent());
        self::assertSame($initialReservations + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_reservation'));

        $client->request('GET', '/circuits/1?user=2');
        $client->submitForm('Publier mon avis', [
            'rating' => 5,
            'comment' => 'Excellent parcours, interface claire et expérience très fluide du début à la fin.',
        ]);
        self::assertResponseRedirects('/circuits/1?user=2');
        $client->followRedirect();
        self::assertStringContainsString('Votre avis a été enregistré', $client->getResponse()->getContent());
        self::assertSame($initialReviews + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_review'));

        $crawler = $client->request('GET', '/circuits-sur-mesure?user=2');
        $customToken = $crawler->filter('form input[name="_token"]')->attr('value');
        $client->request('POST', '/circuits-sur-mesure?user=2', [
            '_token' => $customToken,
            'destination' => 'Kuala Lumpur',
            'date_depart' => '2026-07-02',
            'date_retour' => '2026-07-10',
            'duree' => 8,
            'budget_min' => 1600,
            'budget_max' => 2800,
            'style_voyage' => 'Culture',
            'niveau_fatigue' => 2,
            'centres_interet' => ['Nature'],
        ]);
        self::assertResponseRedirects('/circuits-sur-mesure?user=2');
        $client->followRedirect();
        self::assertStringContainsString('Votre circuit sur mesure a été enregistré', $client->getResponse()->getContent());
        self::assertSame($initialCustom + 1, (int) $connection->fetchOne('SELECT COUNT(*) FROM circuit_personnalise'));
    }

    private function resetDatabase(): void
    {
        $script = $this->projectDir . '/scripts/reset_demo_db.php';
        $dbPath = $this->projectDir . '/var/flyandgo_test.db';
        $command = sprintf('php %s %s', escapeshellarg($script), escapeshellarg($dbPath));
        exec($command, $output, $code);
        self::assertSame(0, $code, 'Impossible de réinitialiser la base de test.');
    }
}
