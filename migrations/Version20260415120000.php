<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reply_to and audio columns to message table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message ADD COLUMN reply_to INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD COLUMN audio VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_MESSAGE_REPLY FOREIGN KEY (reply_to) REFERENCES message(id) ON DELETE SET NULL');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS conversation_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            is_muted TINYINT(1) DEFAULT 0,
            is_archived TINYINT(1) DEFAULT 0,
<<<<<<< HEAD
            is_deleted TINYINT(1) DEFAULT 0,
=======
>>>>>>> testsisi
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_conv_user (conversation_id, user_id)
        )');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS blocked_user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blocker_id INT UNSIGNED NOT NULL,
            blocked_id INT UNSIGNED NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_block (blocker_id, blocked_id)
        )');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS pending_calls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            from_user_id INT UNSIGNED NOT NULL,
            to_user_id INT UNSIGNED NOT NULL,
            call_type VARCHAR(20) DEFAULT "audio",
            status VARCHAR(20) DEFAULT "calling",
            offer TEXT,
            answer TEXT,
            ice_candidate TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_to_user (to_user_id),
            INDEX idx_from_user (from_user_id)
        )');
        
        $this->addSql('CREATE TABLE IF NOT EXISTS conversation_theme (
            conversation_id INT UNSIGNED PRIMARY KEY,
            theme VARCHAR(50) DEFAULT "#0084FF"
        )');
<<<<<<< HEAD
        
        $this->addSql('CREATE TABLE IF NOT EXISTS friend_nickname (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            friend_id INT UNSIGNED NOT NULL,
            nickname VARCHAR(100) DEFAULT NULL,
            UNIQUE KEY unique_user_friend (user_id, friend_id)
        )');
=======
>>>>>>> testsisi
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message DROP COLUMN reply_to');
        $this->addSql('ALTER TABLE message DROP COLUMN audio');
    }
}
