<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217173938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parcelle DROP FOREIGN KEY FK_C56E2CF679F37AE5');
        $this->addSql('DROP INDEX IDX_C56E2CF679F37AE5 ON parcelle');
        $this->addSql('ALTER TABLE parcelle CHANGE id_user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parcelle ADD CONSTRAINT FK_C56E2CF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C56E2CF6A76ED395 ON parcelle (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parcelle DROP FOREIGN KEY FK_C56E2CF6A76ED395');
        $this->addSql('DROP INDEX IDX_C56E2CF6A76ED395 ON parcelle');
        $this->addSql('ALTER TABLE parcelle CHANGE user_id id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parcelle ADD CONSTRAINT FK_C56E2CF679F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C56E2CF679F37AE5 ON parcelle (id_user_id)');
    }
}
