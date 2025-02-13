<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213141033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE type type VARCHAR(50) NOT NULL, CHANGE nbrheure nbrheure INT NOT NULL');
        $this->addSql('ALTER TABLE vente ADD transaction_id INT DEFAULT NULL, ADD produit_id INT DEFAULT NULL, ADD quantite DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactionfinancier (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_888A2A4C2FC0CB0F ON vente (transaction_id)');
        $this->addSql('CREATE INDEX IDX_888A2A4CF347EFB ON vente (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE type type VARCHAR(255) NOT NULL, CHANGE nbrheure nbrheure DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C2FC0CB0F');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('DROP INDEX UNIQ_888A2A4C2FC0CB0F ON vente');
        $this->addSql('DROP INDEX IDX_888A2A4CF347EFB ON vente');
        $this->addSql('ALTER TABLE vente DROP transaction_id, DROP produit_id, DROP quantite');
    }
}
