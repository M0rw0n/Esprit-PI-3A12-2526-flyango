<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version2026041501 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS pending_calls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_user_id INT NOT NULL,
            to_user_id INT NOT NULL,
            call_type VARCHAR(20) NOT NULL DEFAULT "audio",
            sdp TEXT,
            status VARCHAR(20) NOT NULL DEFAULT "calling",
            created_at DATETIME NOT NULL,
            INDEX idx_to_user (to_user_id),
            INDEX idx_status (status)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS pending_calls');
    }
}