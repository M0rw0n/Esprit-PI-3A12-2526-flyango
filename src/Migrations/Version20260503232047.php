<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260503232047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD coordinates_latitude DOUBLE PRECISION DEFAULT NULL, ADD coordinates_longitude DOUBLE PRECISION DEFAULT NULL, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_AC74095AB03A8386 ON activity (created_by_id)');
        $this->addSql('CREATE INDEX IDX_AC74095A896DBBDE ON activity (updated_by_id)');
        $this->addSql('ALTER TABLE app_call ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE app_call ADD CONSTRAINT FK_C9A00A9EB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE app_call ADD CONSTRAINT FK_C9A00A9E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C9A00A9EB03A8386 ON app_call (created_by_id)');
        $this->addSql('CREATE INDEX IDX_C9A00A9E896DBBDE ON app_call (updated_by_id)');
        $this->addSql('ALTER TABLE avis_hebergement ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE avis_hebergement ADD CONSTRAINT FK_17BB9033B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE avis_hebergement ADD CONSTRAINT FK_17BB9033896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_17BB9033B03A8386 ON avis_hebergement (created_by_id)');
        $this->addSql('CREATE INDEX IDX_17BB9033896DBBDE ON avis_hebergement (updated_by_id)');
        $this->addSql('ALTER TABLE booking ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD email_email VARCHAR(180) DEFAULT NULL, DROP email');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEB03A8386 ON booking (created_by_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE896DBBDE ON booking (updated_by_id)');
        $this->addSql('ALTER TABLE booking_transport ADD customer_email_email VARCHAR(180) DEFAULT NULL, DROP customer_email');
        $this->addSql('ALTER TABLE calendar_event ADD date_range_start_date DATE DEFAULT NULL, ADD date_range_end_date DATE DEFAULT NULL, DROP start_date, DROP end_date');
        $this->addSql('ALTER TABLE circuit ADD date_range_start_date DATE DEFAULT NULL, ADD date_range_end_date DATE DEFAULT NULL, ADD coordinates_latitude DOUBLE PRECISION DEFAULT NULL, ADD coordinates_longitude DOUBLE PRECISION DEFAULT NULL, DROP start_date, DROP end_date, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE circuit_avis ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE circuit_avis ADD CONSTRAINT FK_E669CD0DB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE circuit_avis ADD CONSTRAINT FK_E669CD0D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_E669CD0DB03A8386 ON circuit_avis (created_by_id)');
        $this->addSql('CREATE INDEX IDX_E669CD0D896DBBDE ON circuit_avis (updated_by_id)');
        $this->addSql('ALTER TABLE conversation ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9896DBBDE ON conversation (updated_by_id)');
        $this->addSql('ALTER TABLE favorite_activity ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_activity ADD CONSTRAINT FK_62311ABB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite_activity ADD CONSTRAINT FK_62311AB896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_62311ABB03A8386 ON favorite_activity (created_by_id)');
        $this->addSql('CREATE INDEX IDX_62311AB896DBBDE ON favorite_activity (updated_by_id)');
        $this->addSql('ALTER TABLE favorite_circuit ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_circuit ADD CONSTRAINT FK_4E715DC8B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite_circuit ADD CONSTRAINT FK_4E715DC8896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_4E715DC8B03A8386 ON favorite_circuit (created_by_id)');
        $this->addSql('CREATE INDEX IDX_4E715DC8896DBBDE ON favorite_circuit (updated_by_id)');
        $this->addSql('ALTER TABLE favorite_hebergement ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_hebergement ADD CONSTRAINT FK_8B4098DFB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite_hebergement ADD CONSTRAINT FK_8B4098DF896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8B4098DFB03A8386 ON favorite_hebergement (created_by_id)');
        $this->addSql('CREATE INDEX IDX_8B4098DF896DBBDE ON favorite_hebergement (updated_by_id)');
        $this->addSql('ALTER TABLE favorite_post ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_post ADD CONSTRAINT FK_B48C75B2B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite_post ADD CONSTRAINT FK_B48C75B2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_B48C75B2B03A8386 ON favorite_post (created_by_id)');
        $this->addSql('CREATE INDEX IDX_B48C75B2896DBBDE ON favorite_post (updated_by_id)');
        $this->addSql('ALTER TABLE favorite_transport ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE favorite_transport ADD CONSTRAINT FK_ACBBB4BCB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favorite_transport ADD CONSTRAINT FK_ACBBB4BC896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_ACBBB4BCB03A8386 ON favorite_transport (created_by_id)');
        $this->addSql('CREATE INDEX IDX_ACBBB4BC896DBBDE ON favorite_transport (updated_by_id)');
        $this->addSql('ALTER TABLE friend_request ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_F284D94B03A8386 ON friend_request (created_by_id)');
        $this->addSql('CREATE INDEX IDX_F284D94896DBBDE ON friend_request (updated_by_id)');
        $this->addSql('ALTER TABLE hebergement ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD coordinates_latitude DOUBLE PRECISION DEFAULT NULL, ADD coordinates_longitude DOUBLE PRECISION DEFAULT NULL, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE hebergement ADD CONSTRAINT FK_4852DD9CB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE hebergement ADD CONSTRAINT FK_4852DD9C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_4852DD9CB03A8386 ON hebergement (created_by_id)');
        $this->addSql('CREATE INDEX IDX_4852DD9C896DBBDE ON hebergement (updated_by_id)');
        $this->addSql('ALTER TABLE like_dislike ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A689B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A689896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_ADB6A689B03A8386 ON like_dislike (created_by_id)');
        $this->addSql('CREATE INDEX IDX_ADB6A689896DBBDE ON like_dislike (updated_by_id)');
        $this->addSql('ALTER TABLE message ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FB03A8386 ON message (created_by_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F896DBBDE ON message (updated_by_id)');
        $this->addSql('ALTER TABLE message_reaction ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE message_reaction ADD CONSTRAINT FK_ADF1C3E6B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message_reaction ADD CONSTRAINT FK_ADF1C3E6896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_ADF1C3E6B03A8386 ON message_reaction (created_by_id)');
        $this->addSql('CREATE INDEX IDX_ADF1C3E6896DBBDE ON message_reaction (updated_by_id)');
        $this->addSql('DROP INDEX UNIQ_7871BE50E7927C74 ON passport_user');
        $this->addSql('ALTER TABLE passport_user ADD email_email VARCHAR(180) DEFAULT NULL, DROP email');
        $this->addSql('ALTER TABLE profil_voyageur ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profil_voyageur ADD CONSTRAINT FK_E2DB57F9B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE profil_voyageur ADD CONSTRAINT FK_E2DB57F9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_E2DB57F9B03A8386 ON profil_voyageur (created_by_id)');
        $this->addSql('CREATE INDEX IDX_E2DB57F9896DBBDE ON profil_voyageur (updated_by_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD email_email VARCHAR(180) DEFAULT NULL, DROP email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AB03A8386');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A896DBBDE');
        $this->addSql('DROP INDEX IDX_AC74095AB03A8386 ON activity');
        $this->addSql('DROP INDEX IDX_AC74095A896DBBDE ON activity');
        $this->addSql('ALTER TABLE activity ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP created_by_id, DROP updated_by_id, DROP updated_at, DROP coordinates_latitude, DROP coordinates_longitude');
        $this->addSql('ALTER TABLE app_call DROP FOREIGN KEY FK_C9A00A9EB03A8386');
        $this->addSql('ALTER TABLE app_call DROP FOREIGN KEY FK_C9A00A9E896DBBDE');
        $this->addSql('DROP INDEX IDX_C9A00A9EB03A8386 ON app_call');
        $this->addSql('DROP INDEX IDX_C9A00A9E896DBBDE ON app_call');
        $this->addSql('ALTER TABLE app_call DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE avis_hebergement DROP FOREIGN KEY FK_17BB9033B03A8386');
        $this->addSql('ALTER TABLE avis_hebergement DROP FOREIGN KEY FK_17BB9033896DBBDE');
        $this->addSql('DROP INDEX IDX_17BB9033B03A8386 ON avis_hebergement');
        $this->addSql('DROP INDEX IDX_17BB9033896DBBDE ON avis_hebergement');
        $this->addSql('ALTER TABLE avis_hebergement DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB03A8386');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE896DBBDE');
        $this->addSql('DROP INDEX IDX_E00CEDDEB03A8386 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDE896DBBDE ON booking');
        $this->addSql('ALTER TABLE booking ADD email VARCHAR(255) NOT NULL, DROP created_by_id, DROP updated_by_id, DROP email_email');
        $this->addSql('ALTER TABLE booking_transport ADD customer_email VARCHAR(255) DEFAULT NULL, DROP customer_email_email');
        $this->addSql('ALTER TABLE calendar_event ADD start_date DATETIME NOT NULL, ADD end_date DATETIME DEFAULT NULL, DROP date_range_start_date, DROP date_range_end_date');
        $this->addSql('ALTER TABLE circuit ADD start_date DATE DEFAULT NULL, ADD end_date DATE DEFAULT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP date_range_start_date, DROP date_range_end_date, DROP coordinates_latitude, DROP coordinates_longitude');
        $this->addSql('ALTER TABLE circuit_avis DROP FOREIGN KEY FK_E669CD0DB03A8386');
        $this->addSql('ALTER TABLE circuit_avis DROP FOREIGN KEY FK_E669CD0D896DBBDE');
        $this->addSql('DROP INDEX IDX_E669CD0DB03A8386 ON circuit_avis');
        $this->addSql('DROP INDEX IDX_E669CD0D896DBBDE ON circuit_avis');
        $this->addSql('ALTER TABLE circuit_avis DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9896DBBDE');
        $this->addSql('DROP INDEX IDX_8A8E26E9896DBBDE ON conversation');
        $this->addSql('ALTER TABLE conversation DROP updated_by_id');
        $this->addSql('ALTER TABLE favorite_activity DROP FOREIGN KEY FK_62311ABB03A8386');
        $this->addSql('ALTER TABLE favorite_activity DROP FOREIGN KEY FK_62311AB896DBBDE');
        $this->addSql('DROP INDEX IDX_62311ABB03A8386 ON favorite_activity');
        $this->addSql('DROP INDEX IDX_62311AB896DBBDE ON favorite_activity');
        $this->addSql('ALTER TABLE favorite_activity DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE favorite_circuit DROP FOREIGN KEY FK_4E715DC8B03A8386');
        $this->addSql('ALTER TABLE favorite_circuit DROP FOREIGN KEY FK_4E715DC8896DBBDE');
        $this->addSql('DROP INDEX IDX_4E715DC8B03A8386 ON favorite_circuit');
        $this->addSql('DROP INDEX IDX_4E715DC8896DBBDE ON favorite_circuit');
        $this->addSql('ALTER TABLE favorite_circuit DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE favorite_hebergement DROP FOREIGN KEY FK_8B4098DFB03A8386');
        $this->addSql('ALTER TABLE favorite_hebergement DROP FOREIGN KEY FK_8B4098DF896DBBDE');
        $this->addSql('DROP INDEX IDX_8B4098DFB03A8386 ON favorite_hebergement');
        $this->addSql('DROP INDEX IDX_8B4098DF896DBBDE ON favorite_hebergement');
        $this->addSql('ALTER TABLE favorite_hebergement DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE favorite_post DROP FOREIGN KEY FK_B48C75B2B03A8386');
        $this->addSql('ALTER TABLE favorite_post DROP FOREIGN KEY FK_B48C75B2896DBBDE');
        $this->addSql('DROP INDEX IDX_B48C75B2B03A8386 ON favorite_post');
        $this->addSql('DROP INDEX IDX_B48C75B2896DBBDE ON favorite_post');
        $this->addSql('ALTER TABLE favorite_post DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE favorite_transport DROP FOREIGN KEY FK_ACBBB4BCB03A8386');
        $this->addSql('ALTER TABLE favorite_transport DROP FOREIGN KEY FK_ACBBB4BC896DBBDE');
        $this->addSql('DROP INDEX IDX_ACBBB4BCB03A8386 ON favorite_transport');
        $this->addSql('DROP INDEX IDX_ACBBB4BC896DBBDE ON favorite_transport');
        $this->addSql('ALTER TABLE favorite_transport DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94B03A8386');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94896DBBDE');
        $this->addSql('DROP INDEX IDX_F284D94B03A8386 ON friend_request');
        $this->addSql('DROP INDEX IDX_F284D94896DBBDE ON friend_request');
        $this->addSql('ALTER TABLE friend_request DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE hebergement DROP FOREIGN KEY FK_4852DD9CB03A8386');
        $this->addSql('ALTER TABLE hebergement DROP FOREIGN KEY FK_4852DD9C896DBBDE');
        $this->addSql('DROP INDEX IDX_4852DD9CB03A8386 ON hebergement');
        $this->addSql('DROP INDEX IDX_4852DD9C896DBBDE ON hebergement');
        $this->addSql('ALTER TABLE hebergement ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP created_by_id, DROP updated_by_id, DROP updated_at, DROP coordinates_latitude, DROP coordinates_longitude');
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A689B03A8386');
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A689896DBBDE');
        $this->addSql('DROP INDEX IDX_ADB6A689B03A8386 ON like_dislike');
        $this->addSql('DROP INDEX IDX_ADB6A689896DBBDE ON like_dislike');
        $this->addSql('ALTER TABLE like_dislike DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB03A8386');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F896DBBDE');
        $this->addSql('DROP INDEX IDX_B6BD307FB03A8386 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F896DBBDE ON message');
        $this->addSql('ALTER TABLE message DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE message_reaction DROP FOREIGN KEY FK_ADF1C3E6B03A8386');
        $this->addSql('ALTER TABLE message_reaction DROP FOREIGN KEY FK_ADF1C3E6896DBBDE');
        $this->addSql('DROP INDEX IDX_ADF1C3E6B03A8386 ON message_reaction');
        $this->addSql('DROP INDEX IDX_ADF1C3E6896DBBDE ON message_reaction');
        $this->addSql('ALTER TABLE message_reaction DROP created_by_id, DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE passport_user ADD email VARCHAR(180) NOT NULL, DROP email_email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7871BE50E7927C74 ON passport_user (email)');
        $this->addSql('ALTER TABLE profil_voyageur DROP FOREIGN KEY FK_E2DB57F9B03A8386');
        $this->addSql('ALTER TABLE profil_voyageur DROP FOREIGN KEY FK_E2DB57F9896DBBDE');
        $this->addSql('DROP INDEX IDX_E2DB57F9B03A8386 ON profil_voyageur');
        $this->addSql('DROP INDEX IDX_E2DB57F9896DBBDE ON profil_voyageur');
        $this->addSql('ALTER TABLE profil_voyageur DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE `user` ADD email VARCHAR(180) NOT NULL, DROP email_email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON `user` (email)');
    }
}
