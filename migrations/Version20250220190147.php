<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220190147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier ADD nom VARCHAR(100) NOT NULL, ADD description VARCHAR(255) NOT NULL, ADD date_atelier DATE NOT NULL, ADD capacite_max INT NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, ADD statut VARCHAR(100) NOT NULL, ADD role VARCHAR(100) NOT NULL, ADD photo VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier DROP nom, DROP description, DROP date_atelier, DROP capacite_max, DROP prix, DROP statut, DROP role, DROP photo');
    }
}
