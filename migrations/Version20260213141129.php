<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260213141129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE prix_menu prix_menu NUMERIC(10, 2) NOT NULL, CHANGE nombre_personne nombre_personne INT NOT NULL, CHANGE prix_liv prix_liv DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE menu CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(255) NOT NULL, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE code_p code_p VARCHAR(255) NOT NULL, CHANGE ville ville VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE prix_menu prix_menu DOUBLE PRECISION NOT NULL, CHANGE nombre_personne nombre_personne INT DEFAULT NULL, CHANGE prix_liv prix_liv DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE menu CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE code_p code_p VARCHAR(255) DEFAULT NULL, CHANGE ville ville VARCHAR(255) DEFAULT NULL');
    }
}
