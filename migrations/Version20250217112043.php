<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217112043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD photo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT NOT NULL, CHANGE id_produit id_produit INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD description_globale VARCHAR(255) NOT NULL, ADD description_detaille VARCHAR(255) NOT NULL, ADD categorie VARCHAR(255) NOT NULL, CHANGE statut statut VARCHAR(100) NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, DROP quantite, DROP quantite_initiale, DROP quantite_utilise');
        $this->addSql('ALTER TABLE transactionfinancier ADD vente_id INT DEFAULT NULL, DROP description, DROP nbrheure, CHANGE datetransaction date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90513EC37DC7170A ON transactionfinancier (vente_id)');
        $this->addSql('ALTER TABLE user ADD nbr_heures_travail DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE vente ADD produit_id INT DEFAULT NULL, ADD quantite DOUBLE PRECISION NOT NULL, ADD nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_888A2A4CF347EFB ON vente (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP photo');
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT DEFAULT NULL, CHANGE id_produit id_produit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit DROP nom, DROP image, DROP description_globale, DROP description_detaille, DROP categorie, CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE raison_retour raison_retour VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock ADD quantite INT NOT NULL, ADD quantite_initiale DOUBLE PRECISION NOT NULL, ADD quantite_utilise DOUBLE PRECISION NOT NULL, DROP nom, DROP image');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('DROP INDEX UNIQ_90513EC37DC7170A ON transactionfinancier');
        $this->addSql('ALTER TABLE transactionfinancier ADD description VARCHAR(255) NOT NULL, ADD nbrheure DOUBLE PRECISION NOT NULL, DROP vente_id, CHANGE date datetransaction DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user DROP nbr_heures_travail');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('DROP INDEX IDX_888A2A4CF347EFB ON vente');
        $this->addSql('ALTER TABLE vente DROP produit_id, DROP quantite, DROP nom');
    }
}
