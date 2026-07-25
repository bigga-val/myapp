<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260719111447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type_frais (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, code VARCHAR(50) NOT NULL, ordre INT NOT NULL, actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2B4CCB6F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE frais_scolaire ADD type_id INT DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE frais_scolaire ADD CONSTRAINT FK_8E5B45D3C54C8C93 FOREIGN KEY (type_id) REFERENCES type_frais (id)');
        $this->addSql('CREATE INDEX IDX_8E5B45D3C54C8C93 ON frais_scolaire (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE frais_scolaire DROP FOREIGN KEY FK_8E5B45D3C54C8C93');
        $this->addSql('DROP TABLE type_frais');
        $this->addSql('DROP INDEX IDX_8E5B45D3C54C8C93 ON frais_scolaire');
        $this->addSql('ALTER TABLE frais_scolaire ADD type VARCHAR(30) NOT NULL, DROP type_id');
    }
}
