<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180307023632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_distribucion ADD servicio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_distribucion ADD CONSTRAINT FK_7B1C93D571CAA3E7 FOREIGN KEY (servicio_id) REFERENCES n_servicio (id)');
        $this->addSql('CREATE INDEX IDX_7B1C93D571CAA3E7 ON s_distribucion (servicio_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_distribucion DROP FOREIGN KEY FK_7B1C93D571CAA3E7');
        $this->addSql('DROP INDEX IDX_7B1C93D571CAA3E7 ON s_distribucion');
        $this->addSql('ALTER TABLE s_distribucion DROP servicio_id');
    }
}
