<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717123014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bulletin (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, annee_academique_id INT NOT NULL, periode INT NOT NULL, donnees_json JSON NOT NULL COMMENT \'(DC2Type:json)\', moyenne_generale DOUBLE PRECISION DEFAULT NULL, rang_classe INT DEFAULT NULL, mention VARCHAR(50) DEFAULT NULL, genere_par VARCHAR(180) DEFAULT NULL, genere_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', statut VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2B7D8942A6CC7B2 (eleve_id), INDEX IDX_2B7D89428F5EA509 (classe_id), INDEX IDX_2B7D8942B00F076 (annee_academique_id), UNIQUE INDEX unique_bulletin (eleve_id, classe_id, periode, annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE examen (id INT AUTO_INCREMENT NOT NULL, annee_academique_id INT NOT NULL, classe_id INT DEFAULT NULL, libelle VARCHAR(200) NOT NULL, type VARCHAR(30) NOT NULL, periode INT NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_514C8FECB00F076 (annee_academique_id), INDEX IDX_514C8FEC8F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, matiere_id INT NOT NULL, examen_id INT DEFAULT NULL, classe_id INT NOT NULL, annee_academique_id INT NOT NULL, valeur DOUBLE PRECISION NOT NULL, valeur_max DOUBLE PRECISION NOT NULL, periode INT NOT NULL, observations_prof LONGTEXT DEFAULT NULL, saisie_par VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CFBDFA14A6CC7B2 (eleve_id), INDEX IDX_CFBDFA14F46CD258 (matiere_id), INDEX IDX_CFBDFA145C8659A (examen_id), INDEX IDX_CFBDFA148F5EA509 (classe_id), INDEX IDX_CFBDFA14B00F076 (annee_academique_id), UNIQUE INDEX unique_note_eleve_matiere_examen (eleve_id, matiere_id, examen_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D8942A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D89428F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE bulletin ADD CONSTRAINT FK_2B7D8942B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE examen ADD CONSTRAINT FK_514C8FECB00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE examen ADD CONSTRAINT FK_514C8FEC8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA145C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA148F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bulletin DROP FOREIGN KEY FK_2B7D8942A6CC7B2');
        $this->addSql('ALTER TABLE bulletin DROP FOREIGN KEY FK_2B7D89428F5EA509');
        $this->addSql('ALTER TABLE bulletin DROP FOREIGN KEY FK_2B7D8942B00F076');
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FECB00F076');
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FEC8F5EA509');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14A6CC7B2');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14F46CD258');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA145C8659A');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA148F5EA509');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B00F076');
        $this->addSql('DROP TABLE bulletin');
        $this->addSql('DROP TABLE examen');
        $this->addSql('DROP TABLE note');
    }
}
