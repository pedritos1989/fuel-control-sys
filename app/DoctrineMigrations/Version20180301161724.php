<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180301161724 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_carro ADD tarjeta_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE992D8720997 FOREIGN KEY (tarjeta_id) REFERENCES s_tarjeta (id)');
        $this->addSql('CREATE INDEX IDX_774DE992D8720997 ON s_carro (tarjeta_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE992D8720997');
        $this->addSql('DROP INDEX IDX_774DE992D8720997 ON s_carro');
        $this->addSql('ALTER TABLE s_carro DROP tarjeta_id');
    }
}
