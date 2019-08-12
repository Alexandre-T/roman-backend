<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805191108 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE te_book (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, author VARCHAR(255) DEFAULT NULL, biography LONGTEXT DEFAULT NULL, drama_pitch LONGTEXT DEFAULT NULL, tagline_pitch LONGTEXT DEFAULT NULL, title VARCHAR(255) NOT NULL, trajectorial_pitch LONGTEXT DEFAULT NULL, uuid VARCHAR(36) NOT NULL, INDEX idx_book_owner (owner_id), UNIQUE INDEX uk_book_uuid (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ts_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, username VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, UNIQUE INDEX uk_user_mail (email), UNIQUE INDEX uk_user_username (username), UNIQUE INDEX uk_user_uuid (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE te_book ADD CONSTRAINT FK_8D9091987E3C61F9 FOREIGN KEY (owner_id) REFERENCES ts_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_book DROP FOREIGN KEY FK_8D9091987E3C61F9');
        $this->addSql('DROP TABLE te_book');
        $this->addSql('DROP TABLE ts_user');
    }
}