<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180321141639 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_marca (id INT AUTO_INCREMENT NOT NULL, valor VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_carro ADD marca_id INT DEFAULT NULL, DROP modelo');
        $this->addSql('ALTER TABLE s_carro ADD CONSTRAINT FK_774DE99281EF0041 FOREIGN KEY (marca_id) REFERENCES s_marca (id)');
        $this->addSql('CREATE INDEX IDX_774DE99281EF0041 ON s_carro (marca_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_carro DROP FOREIGN KEY FK_774DE99281EF0041');
        $this->addSql('DROP TABLE s_marca');
        $this->addSql('DROP INDEX IDX_774DE99281EF0041 ON s_carro');
        $this->addSql('ALTER TABLE s_carro ADD modelo VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP marca_id');
    }
}
