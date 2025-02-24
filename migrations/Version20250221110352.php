<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221110352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD nbrplacedispo INT DEFAULT NULL, DROP id_user');
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT NOT NULL, CHANGE id_produit id_produit INT NOT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT NOT NULL, CHANGE type_inspection type_inspection VARCHAR(255) NOT NULL, CHANGE inspecteur_id inspecteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD id_user INT NOT NULL, DROP nbrplacedispo');
        $this->addSql('ALTER TABLE avis CHANGE id_client id_client INT DEFAULT NULL, CHANGE id_produit id_produit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT DEFAULT NULL, CHANGE type_inspection type_inspection VARCHAR(255) DEFAULT NULL, CHANGE inspecteur_id inspecteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION DEFAULT NULL, CHANGE raison_retour raison_retour VARCHAR(100) DEFAULT NULL');
    }
}
