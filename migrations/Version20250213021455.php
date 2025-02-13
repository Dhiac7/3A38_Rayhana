<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213021455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP name_client, DROP date_stock, CHANGE id_avis id_client INT NOT NULL');
        $this->addSql('ALTER TABLE dechet CHANGE quantite quantite INT NOT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_inspection id_avis INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD image VARCHAR(255) NOT NULL, DROP prix_promo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD name_client VARCHAR(255) NOT NULL, ADD date_stock DATE NOT NULL, CHANGE id_client id_avis INT NOT NULL');
        $this->addSql('ALTER TABLE dechet CHANGE quantite quantite DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_inspection INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION DEFAULT NULL, CHANGE raison_retour raison_retour VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock ADD prix_promo DOUBLE PRECISION NOT NULL, DROP image');
    }
}
