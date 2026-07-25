<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717131913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE frais_scolaire (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, annee_academique_id INT NOT NULL, libelle VARCHAR(150) NOT NULL, montant DOUBLE PRECISION NOT NULL, type VARCHAR(30) NOT NULL, mois INT DEFAULT NULL, actif TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8E5B45D38F5EA509 (classe_id), INDEX IDX_8E5B45D3B00F076 (annee_academique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement_frais (id INT AUTO_INCREMENT NOT NULL, eleve_id INT NOT NULL, frais_scolaire_id INT NOT NULL, montant_paye DOUBLE PRECISION NOT NULL, date_paiement DATE NOT NULL, mode_paiement VARCHAR(30) NOT NULL, numero_recu VARCHAR(30) NOT NULL, observations LONGTEXT DEFAULT NULL, enregistre_par VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8DD8A98795E172BE (numero_recu), INDEX IDX_8DD8A987A6CC7B2 (eleve_id), INDEX IDX_8DD8A98798397DEB (frais_scolaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE frais_scolaire ADD CONSTRAINT FK_8E5B45D38F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE frais_scolaire ADD CONSTRAINT FK_8E5B45D3B00F076 FOREIGN KEY (annee_academique_id) REFERENCES annee_academique (id)');
        $this->addSql('ALTER TABLE paiement_frais ADD CONSTRAINT FK_8DD8A987A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE paiement_frais ADD CONSTRAINT FK_8DD8A98798397DEB FOREIGN KEY (frais_scolaire_id) REFERENCES frais_scolaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE frais_scolaire DROP FOREIGN KEY FK_8E5B45D38F5EA509');
        $this->addSql('ALTER TABLE frais_scolaire DROP FOREIGN KEY FK_8E5B45D3B00F076');
        $this->addSql('ALTER TABLE paiement_frais DROP FOREIGN KEY FK_8DD8A987A6CC7B2');
        $this->addSql('ALTER TABLE paiement_frais DROP FOREIGN KEY FK_8DD8A98798397DEB');
        $this->addSql('DROP TABLE frais_scolaire');
        $this->addSql('DROP TABLE paiement_frais');
    }
}
