<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302192009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier_likes (id INT AUTO_INCREMENT NOT NULL, atelier_id INT DEFAULT NULL, user_id INT DEFAULT NULL, is_like TINYINT(1) NOT NULL, INDEX IDX_70097A3682E2CF35 (atelier_id), INDEX IDX_70097A36A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier_likes ADD CONSTRAINT FK_70097A3682E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id)');
        $this->addSql('ALTER TABLE atelier_likes ADD CONSTRAINT FK_70097A36A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier_likes DROP FOREIGN KEY FK_70097A3682E2CF35');
        $this->addSql('ALTER TABLE atelier_likes DROP FOREIGN KEY FK_70097A36A76ED395');
        $this->addSql('DROP TABLE atelier_likes');
    }
}
