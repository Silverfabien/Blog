<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229174544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, publish TINYINT NOT NULL, publish_at DATETIME DEFAULT NULL, see INT NOT NULL, author_id INT NOT NULL, author_edit_id INT DEFAULT NULL, INDEX IDX_23A0E66F675F31B (author_id), INDEX IDX_23A0E667BC250E6 (author_edit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E667BC250E6 FOREIGN KEY (author_edit_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE user_api_id user_api_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E667BC250E6');
        $this->addSql('DROP TABLE article');
        $this->addSql('ALTER TABLE user CHANGE user_api_id user_api_id INT NOT NULL');
    }
}
