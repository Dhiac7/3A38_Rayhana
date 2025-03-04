<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303220139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, file_url VARCHAR(255) DEFAULT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, is_available TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD quantite_predite INT DEFAULT NULL, CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION NOT NULL, CHANGE raison_retour raison_retour VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_verified TINYINT(1) DEFAULT NULL, ADD last_wheel_play DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE place');
        $this->addSql('ALTER TABLE produit DROP quantite_predite, CHANGE quantite_vendues quantite_vendues DOUBLE PRECISION DEFAULT NULL, CHANGE raison_retour raison_retour VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE transactionfinancier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP is_verified, DROP last_wheel_play');
    }
}
