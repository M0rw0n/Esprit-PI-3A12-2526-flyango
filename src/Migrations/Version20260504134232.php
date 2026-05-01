<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504134232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set circuit.creator_id to NOT NULL';
    }

    public function up(Schema $schema): void
    {
        $adminId = $this->connection->fetchOne('SELECT id FROM user WHERE role = "ROLE_ADMIN" LIMIT 1');
        if (!$adminId) {
            $adminId = $this->connection->fetchOne('SELECT id FROM user LIMIT 1');
        }

        if ($adminId) {
            $this->addSql('UPDATE circuit SET creator_id = ' . (int)$adminId . ' WHERE creator_id IS NULL');
        }

        $this->addSql('ALTER TABLE circuit CHANGE creator_id creator_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE circuit CHANGE creator_id creator_id INT DEFAULT NULL');
    }
}
