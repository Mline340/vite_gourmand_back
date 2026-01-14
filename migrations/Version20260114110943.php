<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114110943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_menu (commande_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_16693B7082EA2E54 (commande_id), INDEX IDX_16693B70CCD7E912 (menu_id), PRIMARY KEY (commande_id, menu_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commande_menu ADD CONSTRAINT FK_16693B7082EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_menu ADD CONSTRAINT FK_16693B70CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_commande DROP FOREIGN KEY `FK_42BBE3EB82EA2E54`');
        $this->addSql('ALTER TABLE menu_commande DROP FOREIGN KEY `FK_42BBE3EBCCD7E912`');
        $this->addSql('DROP TABLE menu_commande');
        $this->addSql('ALTER TABLE commande CHANGE user_id user_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_commande (menu_id INT NOT NULL, commande_id INT NOT NULL, INDEX IDX_42BBE3EBCCD7E912 (menu_id), INDEX IDX_42BBE3EB82EA2E54 (commande_id), PRIMARY KEY (menu_id, commande_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE menu_commande ADD CONSTRAINT `FK_42BBE3EB82EA2E54` FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_commande ADD CONSTRAINT `FK_42BBE3EBCCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_menu DROP FOREIGN KEY FK_16693B7082EA2E54');
        $this->addSql('ALTER TABLE commande_menu DROP FOREIGN KEY FK_16693B70CCD7E912');
        $this->addSql('DROP TABLE commande_menu');
        $this->addSql('ALTER TABLE commande CHANGE user_id user_id INT DEFAULT NULL');
    }
}
