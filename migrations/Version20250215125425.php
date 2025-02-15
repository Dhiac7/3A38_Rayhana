<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215125425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transactionfinancier ADD vente_id INT DEFAULT NULL, DROP description, DROP nbrheure, CHANGE type type VARCHAR(255) NOT NULL, CHANGE datetransaction date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier ADD CONSTRAINT FK_90513EC37DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90513EC37DC7170A ON transactionfinancier (vente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transactionfinancier DROP FOREIGN KEY FK_90513EC37DC7170A');
        $this->addSql('DROP INDEX UNIQ_90513EC37DC7170A ON transactionfinancier');
        $this->addSql('ALTER TABLE transactionfinancier ADD description VARCHAR(255) NOT NULL, ADD nbrheure INT NOT NULL, DROP vente_id, CHANGE type type VARCHAR(50) NOT NULL, CHANGE date datetransaction DATETIME NOT NULL');
    }
}
