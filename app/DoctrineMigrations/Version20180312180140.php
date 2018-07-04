<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312180140 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recargue (id INT AUTO_INCREMENT NOT NULL, tarjeta_id INT DEFAULT NULL, abastecimiento INT DEFAULT NULL, saldo_inicial INT DEFAULT NULL, saldo_final INT DEFAULT NULL, INDEX IDX_9BD5A419D8720997 (tarjeta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recargue ADD CONSTRAINT FK_9BD5A419D8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id)');
        $this->addSql('ALTER TABLE s_tarjeta DROP abastecimiento');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE recargue');
        $this->addSql('ALTER TABLE s_tarjeta ADD abastecimiento INT NOT NULL');
    }
}
