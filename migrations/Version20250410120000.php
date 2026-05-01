<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250410120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sentiment analysis fields to review table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review ADD COLUMN sentiment_score FLOAT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN sentiment_label VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN sentiment_stars INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN sentiment_confidence FLOAT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD COLUMN sentiment_category VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review DROP COLUMN sentiment_score');
        $this->addSql('ALTER TABLE review DROP COLUMN sentiment_label');
        $this->addSql('ALTER TABLE review DROP COLUMN sentiment_stars');
        $this->addSql('ALTER TABLE review DROP COLUMN sentiment_confidence');
        $this->addSql('ALTER TABLE review DROP COLUMN sentiment_category');
    }
}