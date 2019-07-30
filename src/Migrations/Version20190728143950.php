<?php
/**
 * This file is part of the back-end of Roman application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190728143950 extends AbstractMigration
{
    /**
     * Remove initial tables.
     *
     * @param Schema $schema the initial schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_book DROP CONSTRAINT FK_8D9091987E3C61F9');
        $this->addSql('DROP SEQUENCE te_book_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ts_user_id_seq CASCADE');
        $this->addSql('DROP TABLE te_book');
        $this->addSql('DROP TABLE ts_user');
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Create initial tables.
     *
     * @param Schema $schema the initial schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE te_book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ts_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE te_book (id INT NOT NULL, owner_id INT NOT NULL, author VARCHAR(255) DEFAULT NULL, biography TEXT DEFAULT NULL, drama_pitch TEXT DEFAULT NULL, tagline_pitch TEXT DEFAULT NULL, title VARCHAR(255) NOT NULL, trajectorial_pitch TEXT DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_book_owner ON te_book (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_book_uuid ON te_book (uuid)');
        $this->addSql('COMMENT ON COLUMN te_book.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE ts_user (id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, username VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_user_mail ON ts_user (email)');
        $this->addSql('CREATE UNIQUE INDEX uk_user_username ON ts_user (username)');
        $this->addSql('CREATE UNIQUE INDEX uk_user_uuid ON ts_user (uuid)');
        $this->addSql('COMMENT ON COLUMN ts_user.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE te_book ADD CONSTRAINT FK_8D9091987E3C61F9 FOREIGN KEY (owner_id) REFERENCES ts_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
