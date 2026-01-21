<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121123937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD contact_method VARCHAR(20) DEFAULT NULL, ADD modification_reason LONGTEXT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL, ADD modified_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D99049ECE FOREIGN KEY (modified_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D99049ECE ON commande (modified_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D99049ECE');
        $this->addSql('DROP INDEX IDX_6EEAA67D99049ECE ON commande');
        $this->addSql('ALTER TABLE commande DROP contact_method, DROP modification_reason, DROP modified_at, DROP modified_by_id');
    }
}
