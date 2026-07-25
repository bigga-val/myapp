<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717132800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emprunt_livre (id INT AUTO_INCREMENT NOT NULL, livre_id INT NOT NULL, emprunteur_id INT NOT NULL, date_emprunt DATE NOT NULL, date_retour_prevue DATE NOT NULL, date_retour_effective DATE DEFAULT NULL, statut VARCHAR(20) DEFAULT \'en_cours\' NOT NULL, observations LONGTEXT DEFAULT NULL, enregistre_par VARCHAR(180) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_562087F237D925CB (livre_id), INDEX IDX_562087F2F0840037 (emprunteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, isbn VARCHAR(30) DEFAULT NULL, categorie VARCHAR(100) DEFAULT NULL, annee_publication INT DEFAULT NULL, nombre_exemplaires INT DEFAULT 1 NOT NULL, localisation VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_AC634F99CC1CF4E6 (isbn), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emprunt_livre ADD CONSTRAINT FK_562087F237D925CB FOREIGN KEY (livre_id) REFERENCES livre (id)');
        $this->addSql('ALTER TABLE emprunt_livre ADD CONSTRAINT FK_562087F2F0840037 FOREIGN KEY (emprunteur_id) REFERENCES eleve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emprunt_livre DROP FOREIGN KEY FK_562087F237D925CB');
        $this->addSql('ALTER TABLE emprunt_livre DROP FOREIGN KEY FK_562087F2F0840037');
        $this->addSql('DROP TABLE emprunt_livre');
        $this->addSql('DROP TABLE livre');
    }
}
