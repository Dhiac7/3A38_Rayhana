<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205145647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id_user INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, cin VARCHAR(8) NOT NULL, photo VARCHAR(255) DEFAULT NULL, role VARCHAR(100) NOT NULL, mdp VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, token VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, salaire DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
