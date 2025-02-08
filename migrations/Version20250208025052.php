<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208025052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dechet ADD stock_id INT DEFAULT NULL, ADD stock_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE dechet ADD CONSTRAINT FK_53C0FC60E35482A6 FOREIGN KEY (stock_id_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60DCD6110 ON dechet (stock_id)');
        $this->addSql('CREATE INDEX IDX_53C0FC60E35482A6 ON dechet (stock_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60DCD6110');
        $this->addSql('ALTER TABLE dechet DROP FOREIGN KEY FK_53C0FC60E35482A6');
        $this->addSql('DROP INDEX IDX_53C0FC60DCD6110 ON dechet');
        $this->addSql('DROP INDEX IDX_53C0FC60E35482A6 ON dechet');
        $this->addSql('ALTER TABLE dechet DROP stock_id, DROP stock_id_id');
    }
}
