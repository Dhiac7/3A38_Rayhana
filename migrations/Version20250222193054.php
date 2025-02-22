<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222193054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT NOT NULL, CHANGE id_produit id_produit INT NOT NULL');
        $this->addSql('ALTER TABLE inspection ADD avis_id INT DEFAULT NULL, CHANGE resultat resultat VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE inspection ADD CONSTRAINT FK_F9F13485197E709F FOREIGN KEY (avis_id) REFERENCES avis (id)');
        $this->addSql('CREATE INDEX IDX_F9F13485197E709F ON inspection (avis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT DEFAULT NULL, CHANGE id_produit id_produit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inspection DROP FOREIGN KEY FK_F9F13485197E709F');
        $this->addSql('DROP INDEX IDX_F9F13485197E709F ON inspection');
        $this->addSql('ALTER TABLE inspection DROP avis_id, CHANGE resultat resultat VARCHAR(255) NOT NULL');
    }
}
