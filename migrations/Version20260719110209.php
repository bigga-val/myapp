<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260719110209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE frais_scolaire_classe (frais_scolaire_id INT NOT NULL, classe_id INT NOT NULL, INDEX IDX_F14FE5C098397DEB (frais_scolaire_id), INDEX IDX_F14FE5C08F5EA509 (classe_id), PRIMARY KEY(frais_scolaire_id, classe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE frais_scolaire_classe ADD CONSTRAINT FK_F14FE5C098397DEB FOREIGN KEY (frais_scolaire_id) REFERENCES frais_scolaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE frais_scolaire_classe ADD CONSTRAINT FK_F14FE5C08F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE frais_scolaire DROP FOREIGN KEY FK_8E5B45D38F5EA509');
        $this->addSql('DROP INDEX IDX_8E5B45D38F5EA509 ON frais_scolaire');
        $this->addSql('ALTER TABLE frais_scolaire DROP classe_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE frais_scolaire_classe DROP FOREIGN KEY FK_F14FE5C098397DEB');
        $this->addSql('ALTER TABLE frais_scolaire_classe DROP FOREIGN KEY FK_F14FE5C08F5EA509');
        $this->addSql('DROP TABLE frais_scolaire_classe');
        $this->addSql('ALTER TABLE frais_scolaire ADD classe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE frais_scolaire ADD CONSTRAINT FK_8E5B45D38F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('CREATE INDEX IDX_8E5B45D38F5EA509 ON frais_scolaire (classe_id)');
    }
}
