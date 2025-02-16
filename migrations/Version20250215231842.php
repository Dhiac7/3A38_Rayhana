<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215231842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE produit ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD description_globale VARCHAR(255) NOT NULL, ADD description_detaille VARCHAR(255) NOT NULL, ADD categorie VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE stock DROP quantite, DROP quantite_initiale, DROP quantite_utilise');
        $this->addSql('ALTER TABLE transactionfinancier ADD vente_id INT DEFAULT NULL, DROP description, DROP nbrheure, CHANGE type type VARCHAR(255) NOT NULL, CHANGE datetransaction date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90513EC37DC7170A ON transactionfinancier (vente_id)');
        $this->addSql('ALTER TABLE user ADD nbr_heures_travail DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C2FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_888A2A4C2FC0CB0F ON vente');
        $this->addSql('ALTER TABLE vente ADD nom VARCHAR(255) NOT NULL, DROP transaction_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE date date DATE NOT NULL');
        $this->addSql('ALTER TABLE produit DROP nom, DROP image, DROP description_globale, DROP description_detaille, DROP categorie');
        $this->addSql('ALTER TABLE stock ADD quantite INT NOT NULL, ADD quantite_initiale DOUBLE PRECISION NOT NULL, ADD quantite_utilise DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('DROP INDEX UNIQ_90513EC37DC7170A ON transactionfinancier');
        $this->addSql('ALTER TABLE transactionfinancier ADD description VARCHAR(255) NOT NULL, ADD nbrheure INT NOT NULL, DROP vente_id, CHANGE type type VARCHAR(50) NOT NULL, CHANGE date datetransaction DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user DROP nbr_heures_travail');
        $this->addSql('ALTER TABLE vente ADD transaction_id INT DEFAULT NULL, DROP nom');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactionfinancier (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_888A2A4C2FC0CB0F ON vente (transaction_id)');
    }
}
