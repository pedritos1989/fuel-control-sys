<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301190518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_cantidad_x_plan (id INT AUTO_INCREMENT NOT NULL, plan_id INT DEFAULT NULL, servicio_id INT DEFAULT NULL, cantidad DOUBLE PRECISION NOT NULL, INDEX IDX_B1271B2CE899029B (plan_id), INDEX IDX_B1271B2C71CAA3E7 (servicio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_cantidad_x_plan ADD CONSTRAINT FK_B1271B2CE899029B FOREIGN KEY (plan_id) REFERENCES s_plan_asignacion (id)');
        $this->addSql('ALTER TABLE s_cantidad_x_plan ADD CONSTRAINT FK_B1271B2C71CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('ALTER TABLE s_asignacion_mensual DROP INDEX IDX_2FEC6D5FE899029B, ADD UNIQUE INDEX UNIQ_2FEC6D5FE899029B (plan_id)');
        $this->addSql('ALTER TABLE s_plan_asignacion DROP cantcomb');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE s_cantidad_x_plan');
        $this->addSql('ALTER TABLE s_asignacion_mensual DROP INDEX UNIQ_2FEC6D5FE899029B, ADD INDEX IDX_2FEC6D5FE899029B (plan_id)');
        $this->addSql('ALTER TABLE s_plan_asignacion ADD cantcomb INT NOT NULL');
    }
}
