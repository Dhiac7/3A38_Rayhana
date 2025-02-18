<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217132504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT NOT NULL, CHANGE date_inspection date_inspection DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE type_inspection type_inspection VARCHAR(255) NOT NULL, CHANGE inspecteur_id inspecteur_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT DEFAULT NULL, CHANGE date_inspection date_inspection DATE NOT NULL, CHANGE type_inspection type_inspection VARCHAR(255) DEFAULT NULL, CHANGE inspecteur_id inspecteur_id INT DEFAULT NULL');
    }
}
