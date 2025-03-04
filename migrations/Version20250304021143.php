<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304021143 extends AbstractMigration
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
        $this->addSql('ALTER TABLE atelier ADD title VARCHAR(255) NOT NULL, ADD start_at DATETIME NOT NULL, ADD end_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD atelier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id)');
        $this->addSql('CREATE INDEX IDX_741D53CD82E2CF35 ON place (atelier_id)');
        $this->addSql('ALTER TABLE reservation ADD place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955DA6A219 ON reservation (place_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atelier_likes DROP FOREIGN KEY FK_70097A3682E2CF35');
        $this->addSql('ALTER TABLE atelier_likes DROP FOREIGN KEY FK_70097A36A76ED395');
        $this->addSql('DROP TABLE atelier_likes');
        $this->addSql('ALTER TABLE atelier DROP title, DROP start_at, DROP end_at');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD82E2CF35');
        $this->addSql('DROP INDEX IDX_741D53CD82E2CF35 ON place');
        $this->addSql('ALTER TABLE place DROP atelier_id');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955DA6A219');
        $this->addSql('DROP INDEX UNIQ_42C84955DA6A219 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP place_id');
    }
}
