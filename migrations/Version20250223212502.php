<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223212502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD nbrplacedispo INT DEFAULT NULL, DROP id_user');
        $this->addSql('ALTER TABLE avis DROP id_client, DROP id_produit');
        $this->addSql('ALTER TABLE dechet ADD dechet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DFB6242 FOREIGN KEY (dechet_id) REFERENCES atelier (id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60DFB6242 ON dechet (dechet_id)');
        $this->addSql('ALTER TABLE inspection DROP id_avis, DROP inspecteur_id, CHANGE type_inspection type_inspection VARCHAR(255) DEFAULT NULL, CHANGE resultat resultat VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD created_by_id INT DEFAULT NULL, ADD genre VARCHAR(20) DEFAULT NULL, ADD annee_naissance INT DEFAULT NULL, ADD slug VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B03A8386 ON user (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD id_user INT NOT NULL, DROP nbrplacedispo');
        $this->addSql('ALTER TABLE avis ADD id_client INT NOT NULL, ADD id_produit INT NOT NULL');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DFB6242');
        $this->addSql('DROP INDEX IDX_53C0FC60DFB6242 ON dechet');
        $this->addSql('ALTER TABLE dechet DROP dechet_id');
        $this->addSql('ALTER TABLE inspection ADD id_avis INT NOT NULL, ADD inspecteur_id INT NOT NULL, CHANGE type_inspection type_inspection VARCHAR(255) NOT NULL, CHANGE resultat resultat VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('DROP INDEX IDX_8D93D649B03A8386 ON user');
        $this->addSql('ALTER TABLE user DROP created_by_id, DROP genre, DROP annee_naissance, DROP slug, DROP created_at');
    }
}
