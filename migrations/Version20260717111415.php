<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717111415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annee_academique (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(20) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, is_current TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, user_email VARCHAR(255) DEFAULT NULL, entity_name VARCHAR(100) NOT NULL, entity_id INT DEFAULT NULL, action VARCHAR(20) NOT NULL, old_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', new_data JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ip_address VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, annee_academique_id INT NOT NULL, nom VARCHAR(100) NOT NULL, niveau VARCHAR(255) NOT NULL, effectif_max INT DEFAULT NULL, salle VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8F87BF96B00F076 (annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_credit DATE DEFAULT NULL, raison VARCHAR(255) DEFAULT NULL, taux DOUBLE PRECISION DEFAULT NULL, devise VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debit (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, raison VARCHAR(255) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_debit DATE DEFAULT NULL, taux DOUBLE PRECISION DEFAULT NULL, devise VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(30) DEFAULT NULL, nom VARCHAR(100) NOT NULL, postnom VARCHAR(100) DEFAULT NULL, prenom VARCHAR(100) NOT NULL, date_naissance DATE DEFAULT NULL, lieu_naissance VARCHAR(150) DEFAULT NULL, sexe VARCHAR(1) NOT NULL, photo VARCHAR(255) DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, nom_tuteur VARCHAR(200) DEFAULT NULL, telephone_tuteur VARCHAR(20) DEFAULT NULL, email_tuteur VARCHAR(180) DEFAULT NULL, relation_tuteur VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_ECA105F712B2DC9C (matricule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, nomcomplet VARCHAR(255) DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, dateembauche DATETIME DEFAULT NULL, categorie VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, salaire_journalier DOUBLE PRECISION DEFAULT NULL, specialite VARCHAR(150) DEFAULT NULL, diplome VARCHAR(200) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, statut VARCHAR(20) DEFAULT \'actif\' NOT NULL, niveau_enseignement VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, annee_academique_id INT NOT NULL, date_inscription DATE NOT NULL, statut VARCHAR(20) NOT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5E90F6D6A6CC7B2 (eleve_id), INDEX IDX_5E90F6D68F5EA509 (classe_id), INDEX IDX_5E90F6D6B00F076 (annee_academique_id), UNIQUE INDEX unique_eleve_annee (eleve_id, annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, code VARCHAR(20) DEFAULT NULL, coefficient DOUBLE PRECISION NOT NULL, niveau VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paie (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) DEFAULT NULL, month_pay INT DEFAULT NULL, year_pay SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paie_employe (id INT AUTO_INCREMENT NOT NULL, employe_id INT NOT NULL, paie_id INT NOT NULL, nb_jours INT NOT NULL, salaire_base DOUBLE PRECISION DEFAULT NULL, primes DOUBLE PRECISION DEFAULT NULL, deductions DOUBLE PRECISION DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1CAD089F1B65292 (employe_id), INDEX IDX_1CAD089FB376DD85 (paie_id), UNIQUE INDEX unique_employe_paie (employe_id, paie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taux (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cout DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) DEFAULT NULL, INDEX IDX_809A3D7DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, username VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adressephysique VARCHAR(255) DEFAULT NULL, session_id VARCHAR(255) DEFAULT NULL, devicename VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D68F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089FB376DD85 FOREIGN KEY (paie_id) REFERENCES paie (id)');
        $this->addSql('ALTER TABLE taux ADD CONSTRAINT FK_809A3D7DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B00F076');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A6CC7B2');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D68F5EA509');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6B00F076');
        $this->addSql('ALTER TABLE paie_employe DROP FOREIGN KEY FK_1CAD089F1B65292');
        $this->addSql('ALTER TABLE paie_employe DROP FOREIGN KEY FK_1CAD089FB376DD85');
        $this->addSql('ALTER TABLE taux DROP FOREIGN KEY FK_809A3D7DA76ED395');
        $this->addSql('DROP TABLE annee_academique');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE debit');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE paie');
        $this->addSql('DROP TABLE paie_employe');
        $this->addSql('DROP TABLE taux');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
