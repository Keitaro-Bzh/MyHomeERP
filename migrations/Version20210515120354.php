<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210515120354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banque (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, code_banque INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, code_guichet INT DEFAULT NULL, UNIQUE INDEX UNIQ_B1F6CB3CFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE banque ADD CONSTRAINT FK_B1F6CB3CFCF77503 FOREIGN KEY (societe_id) REFERENCES mycontacts_societes (id)');
        $this->addSql('ALTER TABLE myerp_users DROP avatar');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE banque');
        $this->addSql('ALTER TABLE myerp_users ADD avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
