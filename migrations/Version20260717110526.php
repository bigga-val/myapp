<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717110526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(30) DEFAULT NULL, nom VARCHAR(100) NOT NULL, postnom VARCHAR(100) DEFAULT NULL, prenom VARCHAR(100) NOT NULL, date_naissance DATE DEFAULT NULL, lieu_naissance VARCHAR(150) DEFAULT NULL, sexe VARCHAR(1) NOT NULL, photo VARCHAR(255) DEFAULT NULL, adresse LONGTEXT DEFAULT NULL, nom_tuteur VARCHAR(200) DEFAULT NULL, telephone_tuteur VARCHAR(20) DEFAULT NULL, email_tuteur VARCHAR(180) DEFAULT NULL, relation_tuteur VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_ECA105F712B2DC9C (matricule), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, annee_academique_id INT NOT NULL, date_inscription DATE NOT NULL, statut VARCHAR(20) NOT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5E90F6D6A6CC7B2 (eleve_id), INDEX IDX_5E90F6D68F5EA509 (classe_id), INDEX IDX_5E90F6D6B00F076 (annee_academique_id), UNIQUE INDEX unique_eleve_annee (eleve_id, annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D68F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A6CC7B2');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D68F5EA509');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6B00F076');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE inscription');
    }
}
