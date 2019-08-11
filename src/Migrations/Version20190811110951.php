<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190811110951 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification ADD target VARCHAR(255) NOT NULL');
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

        $this->addSql('ALTER TABLE correction CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help CHANGE language_id language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification DROP target');
        $this->addSql('ALTER TABLE project CHANGE github github VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE website website VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE promotion CHANGE nickname nickname VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE subtask CHANGE task_id task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE project_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE training_course CHANGE status status VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE number number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE promotion_id promotion_id INT DEFAULT NULL, CHANGE tel tel VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zipcode zipcode VARCHAR(5) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE website website VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE github github VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE avatar avatar VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user_data CHANGE content content VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
