<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223211119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dechet ADD dechet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DFB6242 FOREIGN KEY (dechet_id) REFERENCES atelier (id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60DFB6242 ON dechet (dechet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DFB6242');
        $this->addSql('DROP INDEX IDX_53C0FC60DFB6242 ON dechet');
        $this->addSql('ALTER TABLE dechet DROP dechet_id');
    }
}
