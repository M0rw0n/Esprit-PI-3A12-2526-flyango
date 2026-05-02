<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419215902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE circuit ADD CONSTRAINT FK_1325F3A6BC06EA63 FOREIGN KEY (creator) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_1325F3A6BC06EA63 ON circuit (creator)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE circuit DROP FOREIGN KEY FK_1325F3A6BC06EA63');
        $this->addSql('DROP INDEX IDX_1325F3A6BC06EA63 ON circuit');
    }
}
