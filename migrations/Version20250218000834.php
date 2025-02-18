<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218000834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture_agricole ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE culture_agricole ADD CONSTRAINT FK_8B1E4C60A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8B1E4C60A76ED395 ON culture_agricole (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE culture_agricole DROP FOREIGN KEY FK_8B1E4C60A76ED395');
        $this->addSql('DROP INDEX IDX_8B1E4C60A76ED395 ON culture_agricole');
        $this->addSql('ALTER TABLE culture_agricole DROP user_id');
    }
}
