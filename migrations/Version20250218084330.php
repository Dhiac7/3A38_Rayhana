<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218084330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, date_atelier DATE NOT NULL, capacite_max INT NOT NULL, prix DOUBLE PRECISION NOT NULL, id_user INT NOT NULL, statut VARCHAR(100) NOT NULL, role VARCHAR(100) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE atelier_user (atelier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4D145FAA82E2CF35 (atelier_id), INDEX IDX_4D145FAAA76ED395 (user_id), PRIMARY KEY(atelier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, id_client INT NOT NULL, id_produit INT NOT NULL, rate DOUBLE PRECISION NOT NULL, commentaire VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8F91ABF019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE culture_agricole (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_semi DATETIME DEFAULT NULL, superficie DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, rendement_estime DOUBLE PRECISION NOT NULL, INDEX IDX_8B1E4C60DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dechet (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, stock_id_id INT DEFAULT NULL, type VARCHAR(100) NOT NULL, quantite INT NOT NULL, date_production DATE NOT NULL, statut VARCHAR(255) NOT NULL, date_expiration DATE NOT NULL, INDEX IDX_53C0FC60DCD6110 (stock_id), INDEX IDX_53C0FC60E35482A6 (stock_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inspection (id INT AUTO_INCREMENT NOT NULL, avis_id INT DEFAULT NULL, id_avis INT NOT NULL, date_inspection DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type_inspection VARCHAR(255) NOT NULL, inspecteur_id INT NOT NULL, resultat VARCHAR(255) NOT NULL, note INT NOT NULL, INDEX IDX_F9F13485197E709F (avis_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcelle (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, superficie DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, irrigation_disponible VARCHAR(255) NOT NULL, INDEX IDX_C56E2CF6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, stock_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, prix_vente DOUBLE PRECISION NOT NULL, quantite_vendues DOUBLE PRECISION NOT NULL, en_promotion TINYINT(1) NOT NULL, pourcentage_promo INT NOT NULL, date_debut_promo DATETIME NOT NULL, date_fin_promo DATETIME NOT NULL, quantite_retourne DOUBLE PRECISION DEFAULT NULL, date_retour DATE DEFAULT NULL, statut VARCHAR(100) NOT NULL, raison_retour VARCHAR(100) NOT NULL, nom VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, description_globale VARCHAR(255) NOT NULL, description_detaille VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, atelierid INT NOT NULL, id_user INT NOT NULL, date_reservation DATE NOT NULL, mode_paiement VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, nbr_place INT NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_stock DATE NOT NULL, date_expiration DATE NOT NULL, lieu VARCHAR(255) NOT NULL, conditionn VARCHAR(100) NOT NULL, statut VARCHAR(100) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactionfinancier (id INT AUTO_INCREMENT NOT NULL, vente_id INT DEFAULT NULL, user_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_90513EC37DC7170A (vente_id), INDEX IDX_90513EC3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, agriculteur_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, cin VARCHAR(8) NOT NULL, photo VARCHAR(255) DEFAULT NULL, role VARCHAR(100) NOT NULL, mdp VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, token VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, nbr_heures_travail DOUBLE PRECISION DEFAULT NULL, INDEX IDX_8D93D6497EBB810E (agriculteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, date DATETIME NOT NULL, prix DOUBLE PRECISION NOT NULL, methodepayement VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_888A2A4CA76ED395 (user_id), INDEX IDX_888A2A4CF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAA82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE atelier_user ADD CONSTRAINT FK_4D145FAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE culture_agricole ADD CONSTRAINT FK_8B1E4C60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60E35482A6 FOREIGN KEY (stock_id_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485197E709F FOREIGN KEY (avis_id) REFERENCES avis (id)');
        $this->addSql('ALTER TABLE parcelle ADD CONSTRAINT FK_C56E2CF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497EBB810E FOREIGN KEY (agriculteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAA82E2CF35');
        $this->addSql('ALTER TABLE atelier_user DROP FOREIGN KEY FK_4D145FAAA76ED395');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE culture_agricole DROP FOREIGN KEY FK_8B1E4C60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60E35482A6');
        $this->addSql('ALTER TABLE inspection DROP FOREIGN KEY FK_F9F13485197E709F');
        $this->addSql('ALTER TABLE parcelle DROP FOREIGN KEY FK_C56E2CF6A76ED395');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC3A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497EBB810E');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CA76ED395');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE atelier_user');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE culture_agricole');
        $this->addSql('DROP TABLE dechet');
        $this->addSql('DROP TABLE inspection');
        $this->addSql('DROP TABLE parcelle');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE transactionfinancier');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vente');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
