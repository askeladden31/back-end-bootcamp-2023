<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230827005157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collection (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, target_amount NUMERIC(10, 2) NOT NULL, link VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contributor (id INT AUTO_INCREMENT NOT NULL, collection_id_id INT NOT NULL, user_name VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, INDEX IDX_DA6F979338BC2604 (collection_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F979338BC2604 FOREIGN KEY (collection_id_id) REFERENCES collection (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY FK_DA6F979338BC2604');
        $this->addSql('DROP TABLE collection');
        $this->addSql('DROP TABLE contributor');
    }
}
