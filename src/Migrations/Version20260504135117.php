<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504135117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add blameable fields (createdBy, updatedBy, updatedAt) to CircuitAvis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit_avis ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE circuit_avis ADD CONSTRAINT FK_E669CD0DB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE circuit_avis ADD CONSTRAINT FK_E669CD0D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_E669CD0DB03A8386 ON circuit_avis (created_by_id)');
        $this->addSql('CREATE INDEX IDX_E669CD0D896DBBDE ON circuit_avis (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit_avis DROP FOREIGN KEY FK_E669CD0DB03A8386');
        $this->addSql('ALTER TABLE circuit_avis DROP FOREIGN KEY FK_E669CD0D896DBBDE');
        $this->addSql('DROP INDEX IDX_E669CD0DB03A8386 ON circuit_avis');
        $this->addSql('DROP INDEX IDX_E669CD0D896DBBDE ON circuit_avis');
        $this->addSql('ALTER TABLE circuit_avis DROP created_by_id, DROP updated_by_id, DROP updated_at');
    }
}
