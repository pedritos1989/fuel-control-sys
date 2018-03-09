<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180209143947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_plan_asignacion ADD area_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE s_plan_asignacion ADD CONSTRAINT FK_27AE6094BD0F409C FOREIGN KEY (area_id) REFERENCES s_area (id)');
        $this->addSql('CREATE INDEX IDX_27AE6094BD0F409C ON s_plan_asignacion (area_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_plan_asignacion DROP FOREIGN KEY FK_27AE6094BD0F409C');
        $this->addSql('DROP INDEX IDX_27AE6094BD0F409C ON s_plan_asignacion');
        $this->addSql('ALTER TABLE s_plan_asignacion DROP area_id');
    }
}
