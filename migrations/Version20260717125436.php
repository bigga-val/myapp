<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717125436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emploi_du_temps (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, matiere_id INT NOT NULL, enseignant_id INT NOT NULL, annee_academique_id INT NOT NULL, jour_semaine INT NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, salle VARCHAR(50) DEFAULT NULL, actif TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F86B32C18F5EA509 (classe_id), INDEX IDX_F86B32C1F46CD258 (matiere_id), INDEX IDX_F86B32C1E455FCC0 (enseignant_id), INDEX IDX_F86B32C1B00F076 (annee_academique_id), UNIQUE INDEX unique_enseignant_jour_debut_annee (enseignant_id, jour_semaine, heure_debut, annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE presence (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, classe_id INT NOT NULL, date DATE NOT NULL, statut VARCHAR(20) NOT NULL, motif VARCHAR(255) DEFAULT NULL, saisie_par VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6977C7A5A6CC7B2 (eleve_id), INDEX IDX_6977C7A58F5EA509 (classe_id), UNIQUE INDEX unique_eleve_date (eleve_id, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C18F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A5A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE presence ADD CONSTRAINT FK_6977C7A58F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C18F5EA509');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1F46CD258');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1E455FCC0');
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1B00F076');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A5A6CC7B2');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A58F5EA509');
        $this->addSql('DROP TABLE emploi_du_temps');
        $this->addSql('DROP TABLE presence');
    }
}
