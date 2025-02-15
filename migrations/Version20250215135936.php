<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215135936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD photo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE vente ADD nom VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP photo');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION DEFAULT NULL, CHANGE raison_retour raison_retour VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE vente DROP nom');
    }
}
