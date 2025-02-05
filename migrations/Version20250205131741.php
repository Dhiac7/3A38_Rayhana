<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205131741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, date_atelier DATE NOT NULL, capacite_max INT NOT NULL, prix DOUBLE PRECISION NOT NULL, id_user INT NOT NULL, statut VARCHAR(100) NOT NULL, role VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parcelle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, superficie DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, irrigation_disponible VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, cin VARCHAR(8) NOT NULL, photo VARCHAR(255) DEFAULT NULL, role VARCHAR(100) NOT NULL, mdp VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, token VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE culture_agricole ADD rendement_estime DOUBLE PRECISION NOT NULL, DROP rendement, CHANGE date_semi date_semi DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE parcelle');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE culture_agricole ADD rendement VARCHAR(255) NOT NULL, DROP rendement_estime, CHANGE date_semi date_semi VARCHAR(255) NOT NULL');
    }
}
