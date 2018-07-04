<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180318000030 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE s_client_avatar (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, picture LONGBLOB DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, extension VARCHAR(32) DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FDC8B34719EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE s_client_avatar ADD CONSTRAINT FK_FDC8B34719EB6921 FOREIGN KEY (client_id) REFERENCES seg_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE s_client_avatar');
    }
}
