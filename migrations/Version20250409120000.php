<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250409120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug column to circuit table for pretty URLs and API Platform';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit ADD COLUMN slug VARCHAR(200) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BCA6A72E989D9B62 ON circuit (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_BCA6A72E989D9B62 ON circuit');
        $this->addSql('ALTER TABLE circuit DROP COLUMN slug');
    }
}