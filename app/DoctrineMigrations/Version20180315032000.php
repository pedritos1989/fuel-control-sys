<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180315032000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_recargue DROP FOREIGN KEY FK_ADE8A33A9F5A440B');
        $this->addSql('DROP TABLE n_estado_tarjeta');
        $this->addSql('DROP INDEX IDX_ADE8A33A9F5A440B ON s_recargue');
        $this->addSql('ALTER TABLE s_recargue DROP estado_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE n_estado_tarjeta (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_recargue ADD estado_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_recargue ADD CONSTRAINT FK_ADE8A33A9F5A440B FOREIGN KEY (estado_id) REFERENCES n_estado_tarjeta (id)');
        $this->addSql('CREATE INDEX IDX_ADE8A33A9F5A440B ON s_recargue (estado_id)');
    }
}
