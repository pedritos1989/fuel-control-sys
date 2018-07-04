<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180313180424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_distribucion_x_tarjeta (id INT AUTO_INCREMENT NOT NULL, tarjeta_id INT DEFAULT NULL, distribucion_id INT DEFAULT NULL, asignacion INT NOT NULL, INDEX IDX_D04FC7AFD8720997 (tarjeta_id), INDEX IDX_D04FC7AFCCCF7FF5 (distribucion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribucion_tarjeta_solicitud (distribucion_xtarjeta_id INT NOT NULL, solicitud_id INT NOT NULL, INDEX IDX_6DC592B39FEBFE0A (distribucion_xtarjeta_id), INDEX IDX_6DC592B31CB9D6E4 (solicitud_id), PRIMARY KEY(distribucion_xtarjeta_id, solicitud_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_distribucion_x_tarjeta ADD CONSTRAINT FK_D04FC7AFD8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id)');
        $this->addSql('ALTER TABLE s_distribucion_x_tarjeta ADD CONSTRAINT FK_D04FC7AFCCCF7FF5 FOREIGN KEY (distribucion_id) REFERENCES s_distribucion (id)');
        $this->addSql('ALTER TABLE distribucion_tarjeta_solicitud ADD CONSTRAINT FK_6DC592B39FEBFE0A FOREIGN KEY (distribucion_xtarjeta_id) REFERENCES s_distribucion_x_tarjeta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_tarjeta_solicitud ADD CONSTRAINT FK_6DC592B31CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES s_solicitud (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE distribucion_solicitud');
        $this->addSql('DROP TABLE distribucion_tarjeta');
        $this->addSql('ALTER TABLE s_distribucion DROP INDEX UNIQ_7B1C93D5DDB833AD, ADD INDEX IDX_7B1C93D5DDB833AD (plan_asignacion_id)');
        $this->addSql('ALTER TABLE s_distribucion DROP FOREIGN KEY FK_7B1C93D571CAA3E7');
        $this->addSql('DROP INDEX IDX_7B1C93D571CAA3E7 ON s_distribucion');
        $this->addSql('ALTER TABLE s_distribucion ADD fecha DATE NOT NULL, DROP servicio_id, DROP plan, DROP asignacion');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE distribucion_tarjeta_solicitud DROP FOREIGN KEY FK_6DC592B39FEBFE0A');
        $this->addSql('CREATE TABLE distribucion_solicitud (distribucion_id INT NOT NULL, solicitud_id INT NOT NULL, INDEX IDX_D23AAA70CCCF7FF5 (distribucion_id), INDEX IDX_D23AAA701CB9D6E4 (solicitud_id), PRIMARY KEY(distribucion_id, solicitud_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribucion_tarjeta (distribucion_id INT NOT NULL, tarjeta_id INT NOT NULL, INDEX IDX_36A1FD47CCCF7FF5 (distribucion_id), INDEX IDX_36A1FD47D8720997 (tarjeta_id), PRIMARY KEY(distribucion_id, tarjeta_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE distribucion_solicitud ADD CONSTRAINT FK_D23AAA701CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES s_solicitud (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_solicitud ADD CONSTRAINT FK_D23AAA70CCCF7FF5 FOREIGN KEY (distribucion_id) REFERENCES s_distribucion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_tarjeta ADD CONSTRAINT FK_36A1FD47CCCF7FF5 FOREIGN KEY (distribucion_id) REFERENCES s_distribucion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_tarjeta ADD CONSTRAINT FK_36A1FD47D8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE s_distribucion_x_tarjeta');
        $this->addSql('DROP TABLE distribucion_tarjeta_solicitud');
        $this->addSql('ALTER TABLE s_distribucion DROP INDEX IDX_7B1C93D5DDB833AD, ADD UNIQUE INDEX UNIQ_7B1C93D5DDB833AD (plan_asignacion_id)');
        $this->addSql('ALTER TABLE s_distribucion ADD servicio_id INT DEFAULT NULL, ADD plan INT NOT NULL, ADD asignacion INT NOT NULL, DROP fecha');
        $this->addSql('ALTER TABLE s_distribucion ADD CONSTRAINT FK_7B1C93D571CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('CREATE INDEX IDX_7B1C93D571CAA3E7 ON s_distribucion (servicio_id)');
    }
}
