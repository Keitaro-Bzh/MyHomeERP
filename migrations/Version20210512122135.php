<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210512122135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE myfinances_banques (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, UNIQUE INDEX UNIQ_6FC9E99DFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE myfinances_comptes (id INT AUTO_INCREMENT NOT NULL, banque_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, numero INT DEFAULT NULL, solde_initial DOUBLE PRECISION NOT NULL, INDEX IDX_D6AF4DB37E080D9 (banque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE myfinances_banques ADD CONSTRAINT FK_6FC9E99DFCF77503 FOREIGN KEY (societe_id) REFERENCES mycontacts_societes (id)');
        $this->addSql('ALTER TABLE myfinances_comptes ADD CONSTRAINT FK_D6AF4DB37E080D9 FOREIGN KEY (banque_id) REFERENCES myfinances_banques (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE myfinances_comptes DROP FOREIGN KEY FK_D6AF4DB37E080D9');
        $this->addSql('DROP TABLE myfinances_banques');
        $this->addSql('DROP TABLE myfinances_comptes');
    }
}
