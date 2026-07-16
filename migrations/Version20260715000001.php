<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout is_menu sur produits et client_nom/client_tel sur vente';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produits ADD is_menu TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE vente ADD client_nom VARCHAR(255) DEFAULT NULL, ADD client_tel VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produits DROP is_menu');
        $this->addSql('ALTER TABLE vente DROP client_nom, DROP client_tel');
    }
}
