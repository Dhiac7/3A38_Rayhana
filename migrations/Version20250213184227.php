<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213184227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD photo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE avis DROP name_client, DROP date_stock, CHANGE id_avis id_client INT NOT NULL');
        $this->addSql('ALTER TABLE dechet CHANGE quantite quantite INT NOT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_inspection id_avis INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit CHANGE statut statut VARCHAR(100) NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE type type VARCHAR(50) NOT NULL, CHANGE nbrheure nbrheure INT NOT NULL');
        $this->addSql('ALTER TABLE vente ADD user_id INT DEFAULT NULL, ADD transaction_id INT DEFAULT NULL, ADD produit_id INT DEFAULT NULL, ADD quantite DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactionfinancier (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_888A2A4CA76ED395 ON vente (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_888A2A4C2FC0CB0F ON vente (transaction_id)');
        $this->addSql('CREATE INDEX IDX_888A2A4CF347EFB ON vente (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP photo');
        $this->addSql('ALTER TABLE avis ADD name_client VARCHAR(255) NOT NULL, ADD date_stock DATE NOT NULL, CHANGE id_client id_avis INT NOT NULL');
        $this->addSql('ALTER TABLE dechet CHANGE quantite quantite DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_inspection INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DCD6110');
        $this->addSql('ALTER TABLE produit CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE raison_retour raison_retour VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE stock DROP nom, DROP image');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE type type VARCHAR(255) NOT NULL, CHANGE nbrheure nbrheure DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CA76ED395');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C2FC0CB0F');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('DROP INDEX IDX_888A2A4CA76ED395 ON vente');
        $this->addSql('DROP INDEX UNIQ_888A2A4C2FC0CB0F ON vente');
        $this->addSql('DROP INDEX IDX_888A2A4CF347EFB ON vente');
        $this->addSql('ALTER TABLE vente DROP user_id, DROP transaction_id, DROP produit_id, DROP quantite');
    }
}
