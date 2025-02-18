<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218004211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parcelle_culture_agricole (parcelle_id INT NOT NULL, culture_agricole_id INT NOT NULL, INDEX IDX_2EA081A4433ED66 (parcelle_id), INDEX IDX_2EA081A885533F1 (culture_agricole_id), PRIMARY KEY(parcelle_id, culture_agricole_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parcelle_culture_agricole ADD CONSTRAINT FK_2EA081A4433ED66 FOREIGN KEY (parcelle_id) REFERENCES parcelle (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parcelle_culture_agricole ADD CONSTRAINT FK_2EA081A885533F1 FOREIGN KEY (culture_agricole_id) REFERENCES culture_agricole (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parcelle_culture_agricole DROP FOREIGN KEY FK_2EA081A4433ED66');
        $this->addSql('ALTER TABLE parcelle_culture_agricole DROP FOREIGN KEY FK_2EA081A885533F1');
        $this->addSql('DROP TABLE parcelle_culture_agricole');
    }
}
