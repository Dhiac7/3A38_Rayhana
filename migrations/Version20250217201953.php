<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217201953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION NOT NULL, CHANGE pourcentage_promo pourcentage_promo INT DEFAULT NULL, CHANGE date_debut_promo date_debut_promo DATETIME DEFAULT NULL, CHANGE date_fin_promo date_fin_promo DATETIME DEFAULT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION DEFAULT NULL, CHANGE pourcentage_promo pourcentage_promo INT NOT NULL, CHANGE date_debut_promo date_debut_promo DATETIME NOT NULL, CHANGE date_fin_promo date_fin_promo DATETIME NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) DEFAULT NULL');
    }
}
