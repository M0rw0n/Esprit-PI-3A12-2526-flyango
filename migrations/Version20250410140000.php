<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250410140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image fields to forum_post and forum_comment tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_post ADD COLUMN image VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_comment ADD COLUMN image VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_post DROP COLUMN image');
        $this->addSql('ALTER TABLE forum_comment DROP COLUMN image');
    }
}