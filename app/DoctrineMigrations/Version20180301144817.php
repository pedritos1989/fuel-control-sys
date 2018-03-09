<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301144817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_asignacion_mensual (id INT AUTO_INCREMENT NOT NULL, cant_id INT DEFAULT NULL, consecutivo INT NOT NULL, fecha DATE NOT NULL, cantidad DOUBLE PRECISION DEFAULT NULL, INDEX IDX_2FEC6D5FBFC5B73C (cant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_asignacion_mensual ADD CONSTRAINT FK_2FEC6D5FBFC5B73C FOREIGN KEY (cant_id) REFERENCES n_servicio (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE s_asignacion_mensual');
    }
}
