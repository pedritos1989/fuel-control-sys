<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180120170814 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_area (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, responsable VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_carro (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, chofer_id INT DEFAULT NULL, estado_id INT DEFAULT NULL, tipo_id INT DEFAULT NULL, matricula VARCHAR(10) NOT NULL, modelo VARCHAR(255) NOT NULL, insptecn VARCHAR(255) NOT NULL, indcons DOUBLE PRECISION NOT NULL, INDEX IDX_774DE992BD0F409C (area_id), UNIQUE INDEX UNIQ_774DE9922A5E4E82 (chofer_id), INDEX IDX_774DE9929F5A440B (estado_id), INDEX IDX_774DE992A9276E6C (tipo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_chip (id INT AUTO_INCREMENT NOT NULL, tarjeta_id INT DEFAULT NULL, fecha DATETIME NOT NULL, cantcomb DOUBLE PRECISION NOT NULL, saldo_inicial DOUBLE PRECISION NOT NULL, saldo_final DOUBLE PRECISION NOT NULL, INDEX IDX_D5AE571D8720997 (tarjeta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_chofer (id INT AUTO_INCREMENT NOT NULL, ci VARCHAR(11) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, telefono INT DEFAULT NULL, direccion LONGTEXT NOT NULL, licencia VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_distribucion (id INT AUTO_INCREMENT NOT NULL, plan_asignacion_id INT DEFAULT NULL, plan INT NOT NULL, asignacion INT NOT NULL, UNIQUE INDEX UNIQ_7B1C93D5DDB833AD (plan_asignacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribucion_solicitud (distribucion_id INT NOT NULL, solicitud_id INT NOT NULL, INDEX IDX_D23AAA70CCCF7FF5 (distribucion_id), INDEX IDX_D23AAA701CB9D6E4 (solicitud_id), PRIMARY KEY(distribucion_id, solicitud_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribucion_tarjeta (distribucion_id INT NOT NULL, tarjeta_id INT NOT NULL, INDEX IDX_36A1FD47CCCF7FF5 (distribucion_id), INDEX IDX_36A1FD47D8720997 (tarjeta_id), PRIMARY KEY(distribucion_id, tarjeta_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_plan_asignacion (id INT AUTO_INCREMENT NOT NULL, servicio_id INT DEFAULT NULL, fecha DATE NOT NULL, cantcomb INT NOT NULL, INDEX IDX_27AE609471CAA3E7 (servicio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_solicitud (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, fechaHoraS DATETIME NOT NULL, fechaHoraR DATETIME NOT NULL, lugar VARCHAR(255) NOT NULL, motivo LONGTEXT NOT NULL, cantpersona INT NOT NULL, INDEX IDX_348330B5BD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_tarjeta (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, servicio_id INT DEFAULT NULL, numero VARCHAR(4) NOT NULL, lote INT NOT NULL, abastecimiento INT NOT NULL, fechaVenc DATE NOT NULL, INDEX IDX_90DD622CBD0F409C (area_id), INDEX IDX_90DD622C71CAA3E7 (servicio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE n_stado_carro (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql("
            insert into n_stado_carro (valor) values ('En explotación'), ('Roto'), ('Baja')
        ");
        $this->addSql('CREATE TABLE n_servicio (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql("
            insert into n_servicio (valor) values ('Diésel'), ('Gasolina Especial'), ('Gasolina Regular')
        ");
        $this->addSql('CREATE TABLE n_tipo_carro (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_4B019DDB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seg_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9D1644F592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_9D1644F5A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE992BD0F409C FOREIGN KEY (area_id) REFERENCES s_area (id)');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE9922A5E4E82 FOREIGN KEY (chofer_id) REFERENCES s_chofer (id)');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE9929F5A440B FOREIGN KEY (estado_id) REFERENCES n_stado_carro (id)');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE992A9276E6C FOREIGN KEY (tipo_id) REFERENCES n_tipo_carro (id)');
        $this->addSql('ALTER TABLE s_chip ADD CONSTRAINT FK_D5AE571D8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id)');
        $this->addSql('ALTER TABLE s_distribucion ADD CONSTRAINT FK_7B1C93D5DDB833AD FOREIGN KEY (plan_asignacion_id) REFERENCES s_plan_asignacion (id)');
        $this->addSql('ALTER TABLE distribucion_solicitud ADD CONSTRAINT FK_D23AAA70CCCF7FF5 FOREIGN KEY (distribucion_id) REFERENCES s_distribucion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_solicitud ADD CONSTRAINT FK_D23AAA701CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES s_solicitud (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_tarjeta ADD CONSTRAINT FK_36A1FD47CCCF7FF5 FOREIGN KEY (distribucion_id) REFERENCES s_distribucion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE distribucion_tarjeta ADD CONSTRAINT FK_36A1FD47D8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE s_plan_asignacion ADD CONSTRAINT FK_27AE609471CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('ALTER TABLE s_solicitud ADD CONSTRAINT FK_348330B5BD0F409C FOREIGN KEY (area_id) REFERENCES s_area (id)');
        $this->addSql('ALTER TABLE s_tarjeta ADD CONSTRAINT FK_90DD622CBD0F409C FOREIGN KEY (area_id) REFERENCES s_area (id)');
        $this->addSql('ALTER TABLE s_tarjeta ADD CONSTRAINT FK_90DD622C71CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES seg_user (id)');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE992BD0F409C');
        $this->addSql('ALTER TABLE s_solicitud DROP FOREIGN KEY FK_348330B5BD0F409C');
        $this->addSql('ALTER TABLE s_tarjeta DROP FOREIGN KEY FK_90DD622CBD0F409C');
        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE9922A5E4E82');
        $this->addSql('ALTER TABLE distribucion_solicitud DROP FOREIGN KEY FK_D23AAA70CCCF7FF5');
        $this->addSql('ALTER TABLE distribucion_tarjeta DROP FOREIGN KEY FK_36A1FD47CCCF7FF5');
        $this->addSql('ALTER TABLE s_distribucion DROP FOREIGN KEY FK_7B1C93D5DDB833AD');
        $this->addSql('ALTER TABLE distribucion_solicitud DROP FOREIGN KEY FK_D23AAA701CB9D6E4');
        $this->addSql('ALTER TABLE s_chip DROP FOREIGN KEY FK_D5AE571D8720997');
        $this->addSql('ALTER TABLE distribucion_tarjeta DROP FOREIGN KEY FK_36A1FD47D8720997');
        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE9929F5A440B');
        $this->addSql('ALTER TABLE s_plan_asignacion DROP FOREIGN KEY FK_27AE609471CAA3E7');
        $this->addSql('ALTER TABLE s_tarjeta DROP FOREIGN KEY FK_90DD622C71CAA3E7');
        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE992A9276E6C');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
        $this->addSql('DROP TABLE s_area');
        $this->addSql('DROP TABLE s_carro');
        $this->addSql('DROP TABLE s_chip');
        $this->addSql('DROP TABLE s_chofer');
        $this->addSql('DROP TABLE s_distribucion');
        $this->addSql('DROP TABLE distribucion_solicitud');
        $this->addSql('DROP TABLE distribucion_tarjeta');
        $this->addSql('DROP TABLE s_plan_asignacion');
        $this->addSql('DROP TABLE s_solicitud');
        $this->addSql('DROP TABLE s_tarjeta');
        $this->addSql('DROP TABLE n_stado_carro');
        $this->addSql('DROP TABLE n_servicio');
        $this->addSql('DROP TABLE n_tipo_carro');
        $this->addSql('DROP TABLE fos_group');
        $this->addSql('DROP TABLE seg_user');
        $this->addSql('DROP TABLE fos_user_user_group');
    }
}
