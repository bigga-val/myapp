<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228192152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, quantite DOUBLE PRECISION DEFAULT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_reception (id INT AUTO_INCREMENT NOT NULL, commande_produit_id INT DEFAULT NULL, received_by_id INT DEFAULT NULL, reception_date DATETIME DEFAULT NULL, quantite_recue DOUBLE PRECISION DEFAULT NULL, INDEX IDX_40DADF6D97F6521D (commande_produit_id), INDEX IDX_40DADF6D6F8DDD17 (received_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE commande_reception ADD CONSTRAINT FK_40DADF6D97F6521D FOREIGN KEY (commande_produit_id) REFERENCES commande_produit (id)');
        $this->addSql('ALTER TABLE commande_reception ADD CONSTRAINT FK_40DADF6D6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF347EFB');
        $this->addSql('DROP INDEX IDX_6EEAA67DF347EFB ON commande');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande ADD commande_par_id INT DEFAULT NULL, ADD approved_by_id INT DEFAULT NULL, ADD commande_number VARCHAR(255) DEFAULT NULL, ADD is_approved TINYINT(1) DEFAULT NULL, DROP produit_id, DROP user_id, DROP quantite, DROP created_by, DROP raison, DROP approved_by, DROP approved_date, CHANGE commande_date commande_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D44D07D7F FOREIGN KEY (commande_par_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D2D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D44D07D7F ON commande (commande_par_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D2D234F6A ON commande (approved_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('ALTER TABLE commande_reception DROP FOREIGN KEY FK_40DADF6D97F6521D');
        $this->addSql('ALTER TABLE commande_reception DROP FOREIGN KEY FK_40DADF6D6F8DDD17');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE commande_reception');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D44D07D7F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D2D234F6A');
        $this->addSql('DROP INDEX IDX_6EEAA67D44D07D7F ON commande');
        $this->addSql('DROP INDEX IDX_6EEAA67D2D234F6A ON commande');
        $this->addSql('ALTER TABLE commande ADD produit_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD quantite INT DEFAULT NULL, ADD raison VARCHAR(255) DEFAULT NULL, ADD approved_by VARCHAR(255) DEFAULT NULL, ADD approved_date DATETIME NOT NULL, DROP commande_par_id, DROP approved_by_id, DROP is_approved, CHANGE commande_date commande_date DATETIME DEFAULT NULL, CHANGE commande_number created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF347EFB ON commande (produit_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
    }
}
