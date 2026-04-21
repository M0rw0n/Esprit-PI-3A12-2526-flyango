<?php

declare(strict_types=1);

namespace App\Migrations\Passport;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250411000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Passport Quest database schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE passport_user (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            points INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_USER_EMAIL (email),
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE passport_puzzle (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(200) NOT NULL,
            city_name VARCHAR(100) NOT NULL,
            country_name VARCHAR(100) NOT NULL,
            clue TEXT NOT NULL,
            image_filename VARCHAR(255) DEFAULT NULL,
            difficulty VARCHAR(20) NOT NULL DEFAULT "medium",
            order_index INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE passport_user_progress (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            puzzle_id INT NOT NULL,
            is_completed TINYINT(1) NOT NULL DEFAULT 0,
            completion_percentage INT NOT NULL DEFAULT 0,
            score INT NOT NULL DEFAULT 0,
            completed_at DATETIME DEFAULT NULL,
            time_spent INT DEFAULT NULL,
            INDEX IDX_PROGRESS_USER (user_id),
            INDEX IDX_PROGRESS_PUZZLE (puzzle_id),
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE passport_favorite (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            puzzle_id INT NOT NULL,
            added_at DATETIME NOT NULL,
            INDEX IDX_FAV_USER (user_id),
            INDEX IDX_FAV_PUZZLE (puzzle_id),
            UNIQUE INDEX UNIQ_FAV (user_id, puzzle_id),
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE passport_review (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            puzzle_id INT NOT NULL,
            rating INT NOT NULL,
            comment TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_REVIEW_USER (user_id),
            INDEX IDX_REVIEW_PUZZLE (puzzle_id),
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE passport_history (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            puzzle_id INT NOT NULL,
            completed_at DATETIME NOT NULL,
            time_spent INT DEFAULT NULL,
            points_earned INT NOT NULL DEFAULT 0,
            INDEX IDX_HISTORY_USER (user_id),
            INDEX IDX_HISTORY_PUZZLE (puzzle_id),
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE passport_history');
        $this->addSql('DROP TABLE passport_review');
        $this->addSql('DROP TABLE passport_favorite');
        $this->addSql('DROP TABLE passport_user_progress');
        $this->addSql('DROP TABLE passport_puzzle');
        $this->addSql('DROP TABLE passport_user');
    }
}