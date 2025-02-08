<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207221007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, prix_vente DOUBLE PRECISION NOT NULL, quantite_vendues DOUBLE PRECISION NOT NULL, en_promotion TINYINT(1) NOT NULL, pourcentage_promo INT NOT NULL, date_debut_promo DATETIME NOT NULL, date_fin_promo DATETIME NOT NULL, quantite_retourne DOUBLE PRECISION DEFAULT NULL, date_retour DATE DEFAULT NULL, statut VARCHAR(255) NOT NULL, raison_retour VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, date_stock DATE NOT NULL, quantite_initiale DOUBLE PRECISION NOT NULL, quantite_utilise DOUBLE PRECISION NOT NULL, date_expiration DATE NOT NULL, lieu VARCHAR(255) NOT NULL, conditionn VARCHAR(100) NOT NULL, statut VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE culture_agricole ADD stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE culture_agricole ADD CONSTRAINT FK_8B1E4C60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_8B1E4C60DCD6110 ON culture_agricole (stock_id)');
        //$this->addSql('ALTER TABLE dechet ADD stock_id INT DEFAULT NULL, ADD stock_id_id INT DEFAULT NULL, CHANGE type type VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
       // $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60E35482A6 FOREIGN KEY (stock_id_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60DCD6110 ON dechet (stock_id)');
        //$this->addSql('CREATE INDEX IDX_53C0FC60E35482A6 ON dechet (stock_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture_agricole DROP FOREIGN KEY FK_8B1E4C60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60E35482A6');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP INDEX IDX_8B1E4C60DCD6110 ON culture_agricole');
        $this->addSql('ALTER TABLE culture_agricole DROP stock_id');
        $this->addSql('DROP INDEX IDX_53C0FC60DCD6110 ON dechet');
        $this->addSql('DROP INDEX IDX_53C0FC60E35482A6 ON dechet');
        //$this->addSql('ALTER TABLE dechet DROP stock_id, DROP stock_id_id, CHANGE type type VARCHAR(255) NOT NULL');
    }
}
