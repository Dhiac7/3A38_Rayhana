<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216170426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE date date DATE NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
    }
}
