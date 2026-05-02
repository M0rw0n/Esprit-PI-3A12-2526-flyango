<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add facebook_id and google_id columns to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD COLUMN facebook_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD COLUMN google_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649105A3B3 ON `user` (facebook_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491123ABDE ON `user` (google_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_8D93D649105A3B3 ON `user`');
        $this->addSql('DROP INDEX UNIQ_8D93D6491123ABDE ON `user`');
        $this->addSql('ALTER TABLE `user` DROP COLUMN facebook_id');
        $this->addSql('ALTER TABLE `user` DROP COLUMN google_id');
    }
}