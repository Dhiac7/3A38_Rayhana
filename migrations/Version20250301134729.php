<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301134729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Les colonnes existent déjà, donc on ne les ajoute pas à nouveau
        // $this->addSql('ALTER TABLE atelier ADD title VARCHAR(255) NOT NULL, ADD start_at DATETIME DEFAULT NULL, ADD end_at DATETIME DEFAULT NULL');
    }
    
    public function down(Schema $schema): void
    {
        // Les colonnes existent déjà, donc on ne les supprime pas
        // $this->addSql('ALTER TABLE atelier DROP title, DROP start_at, DROP end_at');
    }
}
