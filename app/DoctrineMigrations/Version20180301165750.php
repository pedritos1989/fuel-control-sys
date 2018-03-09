<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301165750 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_plan_asignacion ADD asignacion_mensual_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_plan_asignacion ADD CONSTRAINT FK_27AE609484712DFB FOREIGN KEY (asignacion_mensual_id) REFERENCES s_asignacion_mensual (id)');
        $this->addSql('CREATE INDEX IDX_27AE609484712DFB ON s_plan_asignacion (asignacion_mensual_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_plan_asignacion DROP FOREIGN KEY FK_27AE609484712DFB');
        $this->addSql('DROP INDEX IDX_27AE609484712DFB ON s_plan_asignacion');
        $this->addSql('ALTER TABLE s_plan_asignacion DROP asignacion_mensual_id');
    }
}
