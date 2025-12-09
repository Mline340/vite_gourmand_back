<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209104144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, numero_commande VARCHAR(255) NOT NULL, date_commande DATE NOT NULL, date_prestation DATE NOT NULL, heure_liv TIME NOT NULL, prix_menu DOUBLE PRECISION NOT NULL, nombre_personne INT DEFAULT NULL, prix_liv DOUBLE PRECISION DEFAULT NULL, statut VARCHAR(255) NOT NULL, pret_mat TINYINT DEFAULT NULL, retour_mat TINYINT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, jour VARCHAR(255) DEFAULT NULL, heure_ouverture VARCHAR(255) DEFAULT NULL, heure_fermeture VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, nombre_personne_mini INT NOT NULL, prix_par_personne DOUBLE PRECISION NOT NULL, regime VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, quantite_restante INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_commande (menu_id INT NOT NULL, commande_id INT NOT NULL, INDEX IDX_42BBE3EBCCD7E912 (menu_id), INDEX IDX_42BBE3EB82EA2E54 (commande_id), PRIMARY KEY (menu_id, commande_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE menu_commande ADD CONSTRAINT FK_42BBE3EBCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_commande ADD CONSTRAINT FK_42BBE3EB82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE menu_commande DROP FOREIGN KEY FK_42BBE3EBCCD7E912');
        $this->addSql('ALTER TABLE menu_commande DROP FOREIGN KEY FK_42BBE3EB82EA2E54');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_commande');
    }
}
