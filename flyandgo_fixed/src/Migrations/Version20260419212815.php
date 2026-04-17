<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419212815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE promo_code_usage');
        $this->addSql('ALTER TABLE circuit ADD generated_context LONGTEXT DEFAULT NULL, ADD stops LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD total_distance DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE promo_code_usage (id INT AUTO_INCREMENT NOT NULL, promo_code_id INT NOT NULL, user_id INT DEFAULT NULL, reservation_id INT DEFAULT NULL, reduction_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, used_at DATETIME NOT NULL, INDEX idx_reservation (reservation_id), INDEX idx_promo_user (promo_code_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE circuit DROP generated_context, DROP stops, DROP total_distance');
    }
}
