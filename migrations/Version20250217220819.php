<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217220819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF019EB6921 ON avis (client_id)');
        $this->addSql('ALTER TABLE culture_agricole CHANGE date_semi date_semi DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE inspection ADD avis_id INT DEFAULT NULL, CHANGE date_inspection date_inspection DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485197E709F FOREIGN KEY (avis_id) REFERENCES avis (id)');
        $this->addSql('CREATE INDEX IDX_F9F13485197E709F ON inspection (avis_id)');
        $this->addSql('ALTER TABLE parcelle ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parcelle ADD CONSTRAINT FK_C56E2CF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C56E2CF6A76ED395 ON parcelle (user_id)');
        $this->addSql('ALTER TABLE transactionfinancier ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_90513EC3A76ED395 ON transactionfinancier (user_id)');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('DROP INDEX IDX_8F91ABF019EB6921 ON avis');
        $this->addSql('ALTER TABLE avis DROP client_id');
        $this->addSql('ALTER TABLE culture_agricole CHANGE date_semi date_semi DATETIME NOT NULL');
        $this->addSql('ALTER TABLE inspection DROP FOREIGN KEY FK_F9F13485197E709F');
        $this->addSql('DROP INDEX IDX_F9F13485197E709F ON inspection');
        $this->addSql('ALTER TABLE inspection DROP avis_id, CHANGE date_inspection date_inspection DATE NOT NULL');
        $this->addSql('ALTER TABLE parcelle DROP FOREIGN KEY FK_C56E2CF6A76ED395');
        $this->addSql('DROP INDEX IDX_C56E2CF6A76ED395 ON parcelle');
        $this->addSql('ALTER TABLE parcelle DROP user_id');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC3A76ED395');
        $this->addSql('DROP INDEX IDX_90513EC3A76ED395 ON transactionfinancier');
        $this->addSql('ALTER TABLE transactionfinancier DROP user_id');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4CF347EFB');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }
}
