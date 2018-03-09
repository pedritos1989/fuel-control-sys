<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301184517 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_asignacion_x_servicio (id INT AUTO_INCREMENT NOT NULL, servicio_id INT DEFAULT NULL, asignacion_mensual_id INT DEFAULT NULL, cantidad DOUBLE PRECISION NOT NULL, INDEX IDX_A3DED4A171CAA3E7 (servicio_id), INDEX IDX_A3DED4A184712DFB (asignacion_mensual_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_asignacion_x_servicio ADD CONSTRAINT FK_A3DED4A171CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('ALTER TABLE s_asignacion_x_servicio ADD CONSTRAINT FK_A3DED4A184712DFB FOREIGN KEY (asignacion_mensual_id) REFERENCES s_asignacion_mensual (id)');
        $this->addSql('ALTER TABLE s_asignacion_mensual DROP FOREIGN KEY FK_2FEC6D5F71CAA3E7');
        $this->addSql('DROP INDEX IDX_2FEC6D5F71CAA3E7 ON s_asignacion_mensual');
        $this->addSql('ALTER TABLE s_asignacion_mensual DROP servicio_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE s_asignacion_x_servicio');
        $this->addSql('ALTER TABLE s_asignacion_mensual ADD servicio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_asignacion_mensual ADD CONSTRAINT FK_2FEC6D5F71CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('CREATE INDEX IDX_2FEC6D5F71CAA3E7 ON s_asignacion_mensual (servicio_id)');
    }
}
