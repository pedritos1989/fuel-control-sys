<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180316000214 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_asignacion_x_servicio CHANGE cantidad cantidad INT NOT NULL');
        $this->addSql('ALTER TABLE s_cantidad_x_plan CHANGE cantidad cantidad INT NOT NULL');
        $this->addSql('ALTER TABLE s_chip CHANGE cantcomb cantcomb INT NOT NULL, CHANGE saldo_inicial saldo_inicial INT NOT NULL, CHANGE saldo_final saldo_final INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_asignacion_x_servicio CHANGE cantidad cantidad DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE s_cantidad_x_plan CHANGE cantidad cantidad DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE s_chip CHANGE cantcomb cantcomb DOUBLE PRECISION NOT NULL, CHANGE saldo_inicial saldo_inicial DOUBLE PRECISION NOT NULL, CHANGE saldo_final saldo_final DOUBLE PRECISION NOT NULL');
    }
}
