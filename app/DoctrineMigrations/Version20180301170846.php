<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301170846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_asignacion_mensual DROP FOREIGN KEY FK_2FEC6D5FBFC5B73C');
        $this->addSql('DROP INDEX IDX_2FEC6D5FBFC5B73C ON s_asignacion_mensual');
        $this->addSql('ALTER TABLE s_asignacion_mensual CHANGE cant_id servicio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_asignacion_mensual ADD CONSTRAINT FK_2FEC6D5F71CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('CREATE INDEX IDX_2FEC6D5F71CAA3E7 ON s_asignacion_mensual (servicio_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_asignacion_mensual DROP FOREIGN KEY FK_2FEC6D5F71CAA3E7');
        $this->addSql('DROP INDEX IDX_2FEC6D5F71CAA3E7 ON s_asignacion_mensual');
        $this->addSql('ALTER TABLE s_asignacion_mensual CHANGE servicio_id cant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_asignacion_mensual ADD CONSTRAINT FK_2FEC6D5FBFC5B73C FOREIGN KEY (cant_id) REFERENCES n_servicio (id)');
        $this->addSql('CREATE INDEX IDX_2FEC6D5FBFC5B73C ON s_asignacion_mensual (cant_id)');
    }
}
