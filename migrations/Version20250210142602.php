<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210142602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier_user (atelier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4D145FAA82E2CF35 (atelier_id), INDEX IDX_4D145FAAA76ED395 (user_id), PRIMARY KEY(atelier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, id_produit INT NOT NULL, rate DOUBLE PRECISION NOT NULL, commentaire VARCHAR(255) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inspection (id INT AUTO_INCREMENT NOT NULL, id_avis INT NOT NULL, date_inspection DATE NOT NULL, type_inspection VARCHAR(255) NOT NULL, inspecteur_id INT NOT NULL, resultat VARCHAR(255) NOT NULL, note INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, prix_vente DOUBLE PRECISION NOT NULL, quantite_vendues DOUBLE PRECISION NOT NULL, en_promotion TINYINT(1) NOT NULL, pourcentage_promo INT NOT NULL, date_debut_promo DATETIME NOT NULL, date_fin_promo DATETIME NOT NULL, quantite_retourne DOUBLE PRECISION DEFAULT NULL, date_retour DATE DEFAULT NULL, statut VARCHAR(255) NOT NULL, raison_retour VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, date_stock DATE NOT NULL, quantite_initiale DOUBLE PRECISION NOT NULL, quantite_utilise DOUBLE PRECISION NOT NULL, date_expiration DATE NOT NULL, lieu VARCHAR(255) NOT NULL, conditionn VARCHAR(100) NOT NULL, statut VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAA82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE culture_agricole ADD stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE culture_agricole ADD CONSTRAINT FK_8B1E4C60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_8B1E4C60DCD6110 ON culture_agricole (stock_id)');
        $this->addSql('ALTER TABLE dechet ADD stock_id INT DEFAULT NULL, ADD stock_id_id INT DEFAULT NULL, CHANGE type type VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60E35482A6 FOREIGN KEY (stock_id_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60DCD6110 ON dechet (stock_id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60E35482A6 ON dechet (stock_id_id)');
        $this->addSql('ALTER TABLE user MODIFY id_user INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON user');
        $this->addSql('ALTER TABLE user CHANGE id_user id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture_agricole DROP FOREIGN KEY FK_8B1E4C60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60E35482A6');
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAA82E2CF35');
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAAA76ED395');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('DROP TABLE atelier_user');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE inspection');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP INDEX IDX_8B1E4C60DCD6110 ON culture_agricole');
        $this->addSql('ALTER TABLE culture_agricole DROP stock_id');
        $this->addSql('DROP INDEX IDX_53C0FC60DCD6110 ON dechet');
        $this->addSql('DROP INDEX IDX_53C0FC60E35482A6 ON dechet');
        $this->addSql('ALTER TABLE dechet DROP stock_id, DROP stock_id_id, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON user');
        $this->addSql('ALTER TABLE user CHANGE id id_user INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id_user)');
    }
}
