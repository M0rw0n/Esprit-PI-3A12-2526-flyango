<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database schema for Fly & Go application';
    }

    public function up(Schema $schema): void
    {
        // Create user table
        $this->addSql('CREATE TABLE `user` (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(180) NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM(\'VOYAGEUR\', \'ADMIN\') DEFAULT \'VOYAGEUR\',
            phone VARCHAR(20),
            actif TINYINT(1) DEFAULT 1,
            profile_picture_path VARCHAR(500),
            cover_photo_path VARCHAR(500),
            auth_provider VARCHAR(20) DEFAULT \'LOCAL\',
            external_id VARCHAR(255),
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimeimmutable)\',
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create profil_voyageur table
        $this->addSql('CREATE TABLE profil_voyageur (
            user_id INT NOT NULL,
            destination_preferee VARCHAR(255) NOT NULL,
            type_voyage VARCHAR(50) NOT NULL,
            budget NUMERIC(10, 2) NOT NULL,
            PRIMARY KEY(user_id),
            CONSTRAINT FK_A6E4FF65A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create password_reset_tokens table
        $this->addSql('CREATE TABLE password_reset_tokens (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            token VARCHAR(180) NOT NULL,
            expiration_date DATETIME NOT NULL,
            used TINYINT(1) DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimeimmutable)\',
            UNIQUE INDEX UNIQ_9F0DF8AB5F37A13B (token),
            INDEX IDX_9F0DF8ABA76ED395 (user_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_9F0DF8ABA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop tables in correct order (reverse of creation)
        $this->addSql('DROP TABLE password_reset_tokens');
        $this->addSql('DROP TABLE profil_voyageur');
        $this->addSql('DROP TABLE `user`');
    }
}
