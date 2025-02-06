<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205172036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, date_atelier DATE NOT NULL, capacite_max INT NOT NULL, prix DOUBLE PRECISION NOT NULL, id_user INT NOT NULL, statut VARCHAR(100) NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE culture_agricole (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_semi DATETIME NOT NULL, superficie DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, rendement_estime DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dechet (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, date_production DATE NOT NULL, statut VARCHAR(255) NOT NULL, date_expiration DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcelle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, superficie DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, irrigation_disponible VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, atelierid INT NOT NULL, id_user INT NOT NULL, date_reservation DATE NOT NULL, mode_paiement VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, nbr_place INT NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id_user INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, cin VARCHAR(8) NOT NULL, photo VARCHAR(255) DEFAULT NULL, role VARCHAR(100) NOT NULL, mdp VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, token VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE culture_agricole');
        $this->addSql('DROP TABLE dechet');
        $this->addSql('DROP TABLE parcelle');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
