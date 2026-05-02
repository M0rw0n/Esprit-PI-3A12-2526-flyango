<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stops and total_distance columns to circuit table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit ADD COLUMN stops JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE circuit ADD COLUMN total_distance INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit DROP COLUMN stops');
        $this->addSql('ALTER TABLE circuit DROP COLUMN total_distance');
    }
}