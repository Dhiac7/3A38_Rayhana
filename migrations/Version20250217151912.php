<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217151912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF019EB6921 ON avis (client_id)');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT NOT NULL, CHANGE type_inspection type_inspection VARCHAR(255) NOT NULL, CHANGE inspecteur_id inspecteur_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('DROP INDEX IDX_8F91ABF019EB6921 ON avis');
        $this->addSql('ALTER TABLE avis DROP client_id');
        $this->addSql('ALTER TABLE inspection CHANGE id_avis id_avis INT DEFAULT NULL, CHANGE type_inspection type_inspection VARCHAR(255) DEFAULT NULL, CHANGE inspecteur_id inspecteur_id INT DEFAULT NULL');
    }
}
