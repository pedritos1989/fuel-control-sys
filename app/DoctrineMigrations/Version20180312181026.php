<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312181026 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recargue ADD fecha DATETIME NOT NULL, DROP abastecimiento, DROP saldo_inicial, DROP saldo_final');
        $this->addSql('ALTER TABLE s_tarjeta ADD abastecimiento INT DEFAULT NULL, ADD saldo_inicial INT DEFAULT NULL, ADD saldo_final INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recargue ADD abastecimiento INT DEFAULT NULL, ADD saldo_inicial INT DEFAULT NULL, ADD saldo_final INT DEFAULT NULL, DROP fecha');
        $this->addSql('ALTER TABLE s_tarjeta DROP abastecimiento, DROP saldo_inicial, DROP saldo_final');
    }
}
