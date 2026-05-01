<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260418002104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Tables already dropped manually
        // $this->addSql('DROP TABLE IF EXISTS conversation_settings');
        // $this->addSql('DROP TABLE IF EXISTS conversation_theme');
        // $this->addSql('DROP TABLE IF EXISTS friend_nickname');
        
        // Columns already exist
        // $this->addSql('ALTER TABLE hebergement ADD blocked_dates LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD chambres_disponibles INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE reservation_hebergement CHANGE statut statut VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation_settings (id INT AUTO_INCREMENT NOT NULL, conversation_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, is_muted TINYINT(1) DEFAULT 0, is_archived TINYINT(1) DEFAULT 0, is_deleted TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX unique_conv_user (conversation_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE conversation_theme (conversation_id INT UNSIGNED NOT NULL, theme VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'#0084FF\' COLLATE `utf8mb4_general_ci`, PRIMARY KEY(conversation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE friend_nickname (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, friend_id INT UNSIGNED NOT NULL, nickname VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX unique_user_friend (user_id, friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE hebergement DROP blocked_dates, DROP chambres_disponibles');
        $this->addSql('ALTER TABLE reservation_hebergement CHANGE statut statut VARCHAR(20) DEFAULT \'EN_ATTENTE\'');
    }
}
