<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190808151425 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifiable_notification DROP FOREIGN KEY FK_ADCFE0FAC3A0A4F8');
        $this->addSql('CREATE TABLE user_notif (id INT AUTO_INCREMENT NOT NULL, receiver_id INT NOT NULL, notification_id INT NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_22B1F633CD53EDB6 (receiver_id), INDEX IDX_22B1F633EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_notif ADD CONSTRAINT FK_22B1F633CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_notif ADD CONSTRAINT FK_22B1F633EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('DROP TABLE notifiable_entity');
        $this->addSql('DROP TABLE notifiable_notification');
        $this->addSql('ALTER TABLE notification ADD sender_id INT NOT NULL, DROP subject, DROP message, DROP link, CHANGE date sent_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF624B39D ON notification (sender_id)');
        $this->addSql('ALTER TABLE project CHANGE github github VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE promotion_id promotion_id INT DEFAULT NULL, CHANGE tel tel VARCHAR(10) DEFAULT NULL, CHANGE zipcode zipcode VARCHAR(5) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE github github VARCHAR(255) DEFAULT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE correction CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promotion CHANGE nickname nickname VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE training_course CHANGE status status VARCHAR(50) DEFAULT NULL, CHANGE number number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help CHANGE language_id language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_data CHANGE content content VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE subtask CHANGE task_id task_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notifiable_entity (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, class VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE notifiable_notification (id INT AUTO_INCREMENT NOT NULL, notification_id INT DEFAULT NULL, notifiable_entity_id INT DEFAULT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_ADCFE0FAC3A0A4F8 (notifiable_entity_id), INDEX IDX_ADCFE0FAEF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE notifiable_notification ADD CONSTRAINT FK_ADCFE0FAC3A0A4F8 FOREIGN KEY (notifiable_entity_id) REFERENCES notifiable_entity (id)');
        $this->addSql('ALTER TABLE notifiable_notification ADD CONSTRAINT FK_ADCFE0FAEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('DROP TABLE user_notif');
        $this->addSql('ALTER TABLE correction CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help CHANGE language_id language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF624B39D');
        $this->addSql('DROP INDEX IDX_BF5476CAF624B39D ON notification');
        $this->addSql('ALTER TABLE notification ADD subject VARCHAR(4000) NOT NULL COLLATE utf8mb4_unicode_ci, ADD message VARCHAR(4000) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, ADD link VARCHAR(4000) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, DROP sender_id, CHANGE sent_at date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE github github VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE website website VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE promotion CHANGE nickname nickname VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE subtask CHANGE task_id task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE training_course CHANGE status status VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE number number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE promotion_id promotion_id INT DEFAULT NULL, CHANGE tel tel VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zipcode zipcode VARCHAR(5) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE website website VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE github github VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE avatar avatar VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user_data CHANGE content content VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
