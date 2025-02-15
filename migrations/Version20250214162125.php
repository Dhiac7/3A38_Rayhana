<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214162125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD photo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD description_globale VARCHAR(255) NOT NULL, ADD description_detaille VARCHAR(255) NOT NULL, ADD categorie VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE stock DROP quantite, DROP quantite_initiale, DROP quantite_utilise');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP photo');
        $this->addSql('ALTER TABLE produit DROP nom, DROP image, DROP description_globale, DROP description_detaille, DROP categorie');
        $this->addSql('ALTER TABLE stock ADD quantite INT NOT NULL, ADD quantite_initiale DOUBLE PRECISION NOT NULL, ADD quantite_utilise DOUBLE PRECISION NOT NULL');
    }
}
