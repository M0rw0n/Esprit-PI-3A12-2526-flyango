<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408204000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add payment fields to transport booking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking_transport ADD booking_ref VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking_transport ADD payment_intent_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking_transport ADD payment_method VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking_transport DROP COLUMN booking_ref');
        $this->addSql('ALTER TABLE booking_transport DROP COLUMN payment_intent_id');
        $this->addSql('ALTER TABLE booking_transport DROP COLUMN payment_method');
    }
}