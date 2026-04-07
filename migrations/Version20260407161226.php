<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407161226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les doublons en gardant la ligne avec le plus grand id
        $this->addSql('DELETE p1 FROM paie_employe p1 INNER JOIN paie_employe p2 WHERE p1.employe_id = p2.employe_id AND p1.paie_id = p2.paie_id AND p1.id < p2.id');

        // Créer l'index unique
        $this->addSql('CREATE UNIQUE INDEX unique_employe_paie ON paie_employe (employe_id, paie_id)');

        // Recréer les FK (supprimées lors de tentatives précédentes)
        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089FB376DD85 FOREIGN KEY (paie_id) REFERENCES paie (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employe DROP salaire_journalier');

        $this->addSql('ALTER TABLE paie_employe DROP FOREIGN KEY FK_1CAD089F1B65292');
        $this->addSql('ALTER TABLE paie_employe DROP FOREIGN KEY FK_1CAD089FAF4A0614');

        $this->addSql('DROP INDEX unique_employe_paie ON paie_employe');
        $this->addSql('ALTER TABLE paie_employe DROP nb_jours, DROP salaire_base, DROP primes, DROP deductions, CHANGE employe_id employe_id INT DEFAULT NULL, CHANGE paie_id paie_id INT DEFAULT NULL, CHANGE total total NUMERIC(10, 2) DEFAULT NULL');

        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE paie_employe ADD CONSTRAINT FK_1CAD089FAF4A0614 FOREIGN KEY (paie_id) REFERENCES paie (id)');
    }
}
