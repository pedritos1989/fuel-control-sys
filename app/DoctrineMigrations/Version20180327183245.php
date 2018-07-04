<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180327183245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE n_persona (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE seg_user ADD persona_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seg_user ADD CONSTRAINT FK_9D1644F5F5F88DB9 FOREIGN KEY (persona_id) REFERENCES n_persona (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D1644F5F5F88DB9 ON seg_user (persona_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE seg_user DROP FOREIGN KEY FK_9D1644F5F5F88DB9');
        $this->addSql('DROP TABLE n_persona');
        $this->addSql('DROP INDEX UNIQ_9D1644F5F5F88DB9 ON seg_user');
        $this->addSql('ALTER TABLE seg_user DROP persona_id');
    }
}
