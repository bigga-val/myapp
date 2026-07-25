<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717105009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annee_academique (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(20) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, is_current TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, annee_academique_id INT NOT NULL, nom VARCHAR(100) NOT NULL, niveau VARCHAR(255) NOT NULL, effectif_max INT DEFAULT NULL, salle VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8F87BF96B00F076 (annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, code VARCHAR(20) DEFAULT NULL, coefficient DOUBLE PRECISION NOT NULL, niveau VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE approvisionnement DROP FOREIGN KEY FK_516C3FAAF347EFB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D2D234F6A');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D44D07D7F');
        $this->addSql('ALTER TABLE commande_approbateur DROP FOREIGN KEY FK_8121C2D6A76ED395');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commande_reception DROP FOREIGN KEY FK_40DADF6D6F8DDD17');
        $this->addSql('ALTER TABLE commande_reception DROP FOREIGN KEY FK_40DADF6D97F6521D');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8CBCF5E72D');
        $this->addSql('ALTER TABLE produit_vendu DROP FOREIGN KEY FK_EF634FEDF347EFB');
        $this->addSql('ALTER TABLE produit_vendu DROP FOREIGN KEY FK_EF634FED7DC7170A');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C96390B6A');
        $this->addSql('DROP TABLE approvisionnement');
        $this->addSql('DROP TABLE categorie_produit');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_approbateur');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE commande_reception');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE produit_vendu');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE vente');
        $this->addSql('ALTER TABLE employe ADD specialite VARCHAR(150) DEFAULT NULL, ADD diplome VARCHAR(200) DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD adresse LONGTEXT DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT NULL, ADD statut VARCHAR(20) DEFAULT \'actif\' NOT NULL, ADD niveau_enseignement VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE approvisionnement (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, qty DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, appro_date DATE DEFAULT NULL, cout DOUBLE PRECISION DEFAULT NULL, taux DOUBLE PRECISION DEFAULT NULL, type VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, motif VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_516C3FAAF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categorie_produit (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, pourcentage DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, commande_par_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, commande_date DATETIME NOT NULL, commande_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_approved TINYINT(1) DEFAULT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6EEAA67D44D07D7F (commande_par_id), INDEX IDX_6EEAA67D2D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande_approbateur (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_8121C2D6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, quantite DOUBLE PRECISION DEFAULT NULL, prix_unitaire DOUBLE PRECISION DEFAULT NULL, taux DOUBLE PRECISION DEFAULT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commande_reception (id INT AUTO_INCREMENT NOT NULL, commande_produit_id INT DEFAULT NULL, received_by_id INT DEFAULT NULL, reception_date DATETIME DEFAULT NULL, quantite_recue DOUBLE PRECISION DEFAULT NULL, INDEX IDX_40DADF6D97F6521D (commande_produit_id), INDEX IDX_40DADF6D6F8DDD17 (received_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, designation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, fabricant VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, preemption DATETIME DEFAULT NULL, prix DOUBLE PRECISION DEFAULT NULL, code VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, unite_mesure VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, maximum DOUBLE PRECISION DEFAULT NULL, minimum DOUBLE PRECISION DEFAULT NULL, image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_menu TINYINT(1) DEFAULT 0, INDEX IDX_BE2DDF8CBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produit_vendu (id INT AUTO_INCREMENT NOT NULL, vente_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, qty DOUBLE PRECISION DEFAULT NULL, createdby VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', prix_unitaire DOUBLE PRECISION DEFAULT NULL, taux DOUBLE PRECISION DEFAULT NULL, INDEX IDX_EF634FEDF347EFB (produit_id), INDEX IDX_EF634FED7DC7170A (vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, table_servie_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, numero_vente VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, vente_date DATE DEFAULT NULL, is_approved TINYINT(1) DEFAULT NULL, status_vente VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, client_nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, client_tel VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_888A2A4C96390B6A (table_servie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE approvisionnement ADD CONSTRAINT FK_516C3FAAF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D2D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D44D07D7F FOREIGN KEY (commande_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande_approbateur ADD CONSTRAINT FK_8121C2D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE commande_reception ADD CONSTRAINT FK_40DADF6D6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande_reception ADD CONSTRAINT FK_40DADF6D97F6521D FOREIGN KEY (commande_produit_id) REFERENCES commande_produit (id)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_produit (id)');
        $this->addSql('ALTER TABLE produit_vendu ADD CONSTRAINT FK_EF634FEDF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE produit_vendu ADD CONSTRAINT FK_EF634FED7DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C96390B6A FOREIGN KEY (table_servie_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B00F076');
        $this->addSql('DROP TABLE annee_academique');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('ALTER TABLE employe DROP specialite, DROP diplome, DROP telephone, DROP adresse, DROP photo, DROP statut, DROP niveau_enseignement');
    }
}
