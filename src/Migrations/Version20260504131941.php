<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504131941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update entity mappings to match existing FK column names (no DB changes needed)';
    }

    public function up(Schema $schema): void
    {
        // Database columns already correctly named:
        // - message.reply_to_id
        // - circuit.creator_id
        // - reservation_hebergement.hebergement_id
        // - avis_hebergement.hebergement_id
        // - circuit_reservation.circuit_id
        // - circuit_avis.circuit_id
        // No ALTER TABLE needed - only entity mapping updates
    }

    public function down(Schema $schema): void
    {
        // No changes to revert
    }
}
