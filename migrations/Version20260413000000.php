<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260413000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create messaging tables: conversation, message, conversation_participant';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE conversation (
            id INT AUTO_INCREMENT NOT NULL,
            type VARCHAR(50) NOT NULL,
            name VARCHAR(255) DEFAULT NULL,
            image VARCHAR(500) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            created_by_id INT DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX idx_type (type)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE message (
            id INT AUTO_INCREMENT NOT NULL,
            conversation_id INT NOT NULL,
            sender_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT \'sent\',
            created_at DATETIME NOT NULL,
            read_at DATETIME DEFAULT NULL,
            image VARCHAR(500) DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX idx_conversation (conversation_id),
            INDEX idx_sender (sender_id),
            INDEX idx_status (status)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE conversation_participant (
            id INT AUTO_INCREMENT NOT NULL,
            conversation_id INT NOT NULL,
            user_id INT NOT NULL,
            unread_count INT NOT NULL DEFAULT 0,
            last_read_at DATETIME DEFAULT NULL,
            joined_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX idx_conversation (conversation_id),
            INDEX idx_user (user_id),
            UNIQUE INDEX unique_conv_user (conversation_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_CONVERSATION_CREATOR 
            FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_MESSAGE_CONVERSATION 
            FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_MESSAGE_SENDER 
            FOREIGN KEY (sender_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE conversation_participant ADD CONSTRAINT FK_PARTICIPANT_CONVERSATION 
            FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE conversation_participant ADD CONSTRAINT FK_PARTICIPANT_USER 
            FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE conversation_participant');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE conversation');
    }
}