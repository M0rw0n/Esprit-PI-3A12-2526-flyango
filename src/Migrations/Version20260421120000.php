<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260421120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add photos360 column to hebergement table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hebergement ADD photos360 JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hebergement DROP photos360');
    }
}