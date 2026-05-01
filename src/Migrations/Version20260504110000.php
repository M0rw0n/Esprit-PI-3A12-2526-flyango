<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Unify all table collations to utf8mb4_unicode_ci and fix timezone settings';
    }

    public function up(Schema $schema): void
    {
        $tables = [
            'activity', 'app_call', 'avis_hebergement', 'avis_transport', 'booking',
            'booking_transport', 'calendar_event', 'circuit', 'circuit_avis', 'circuit_reservation',
            'conversation', 'conversation_participant', 'doctrine_migration_versions', 'faq',
            'favorite_activity', 'favorite_circuit', 'favorite_hebergement', 'favorite_post', 'favorite_transport',
            'forum_comment', 'forum_post', 'friend_request', 'hebergement',
            'like_dislike', 'message', 'message_reaction', 'passport_favorite', 'passport_history',
            'passport_puzzle', 'passport_review', 'passport_user', 'passport_user_progress',
            'profil_voyageur', 'promo_code', 'reservation_hebergement', 'review',
            'story', 'story_reaction', 'story_view', 'transport_details', 'user'
        ];

        foreach ($tables as $table) {
            $this->connection->executeStatement(
                sprintf('ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $table)
            );
        }

        $this->connection->executeStatement('ALTER DATABASE `pidev3a29` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        $tables = [
            'activity', 'app_call', 'avis_hebergement', 'avis_transport', 'booking',
            'booking_transport', 'calendar_event', 'circuit', 'circuit_avis', 'circuit_reservation',
            'conversation', 'conversation_participant', 'doctrine_migration_versions', 'faq',
            'favorite_activity', 'favorite_circuit', 'favorite_hebergement', 'favorite_post', 'favorite_transport',
            'forum_comment', 'forum_post', 'friend_request', 'hebergement',
            'like_dislike', 'message', 'message_reaction', 'passport_favorite', 'passport_history',
            'passport_puzzle', 'passport_review', 'passport_user', 'passport_user_progress',
            'profil_voyageur', 'promo_code', 'reservation_hebergement', 'review',
            'story', 'story_reaction', 'story_view', 'transport_details', 'user'
        ];

        foreach ($tables as $table) {
            $this->connection->executeStatement(
                sprintf('ALTER TABLE `%s` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci', $table)
            );
        }

        $this->connection->executeStatement('ALTER DATABASE `pidev3a29` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci');
    }
}
