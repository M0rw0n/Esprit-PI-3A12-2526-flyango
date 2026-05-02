<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create messaging tables: conversation, conversation_participant, message';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS conversation (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL DEFAULT \'private\',
            name VARCHAR(255) NULL,
            image VARCHAR(500) NULL,
            created_by_id INT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            INDEX idx_created_by (created_by_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS conversation_participant (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT NOT NULL,
            user_id INT NOT NULL,
            unread_count INT DEFAULT 0,
            last_read_at DATETIME NULL,
            joined_at DATETIME NOT NULL,
            INDEX idx_conv_user (conversation_id, user_id),
            INDEX idx_user (user_id),
            CONSTRAINT fk_conv_participant_conv FOREIGN KEY (conversation_id) REFERENCES conversation(id) ON DELETE CASCADE,
            CONSTRAINT fk_conv_participant_user FOREIGN KEY (user_id) REFERENCES `user`(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS message (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT NOT NULL,
            sender_id INT NOT NULL,
            content TEXT NOT NULL,
            status VARCHAR(20) DEFAULT \'sent\',
            created_at DATETIME NOT NULL,
            read_at DATETIME NULL,
            image VARCHAR(500) NULL,
            INDEX idx_msg_conv (conversation_id),
            INDEX idx_msg_sender (sender_id),
            CONSTRAINT fk_message_conv FOREIGN KEY (conversation_id) REFERENCES conversation(id) ON DELETE CASCADE,
            CONSTRAINT fk_message_sender FOREIGN KEY (sender_id) REFERENCES `user`(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS message');
        $this->addSql('DROP TABLE IF EXISTS conversation_participant');
        $this->addSql('DROP TABLE IF EXISTS conversation');
    }
}
