<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516132352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE myfinances_comptes (id INT AUTO_INCREMENT NOT NULL, banque_id INT NOT NULL, titulaire_id INT NOT NULL, cotitulaire_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, numero INT DEFAULT NULL, solde_initial DOUBLE PRECISION NOT NULL, type_compte VARCHAR(5) NOT NULL, date_creation DATETIME DEFAULT NULL, id_creation INT DEFAULT NULL, date_modification DATETIME DEFAULT NULL, id_modification INT DEFAULT NULL, INDEX IDX_D6AF4DB37E080D9 (banque_id), INDEX IDX_D6AF4DBA10273AA (titulaire_id), INDEX IDX_D6AF4DBD253699B (cotitulaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE myfinances_comptes ADD CONSTRAINT FK_D6AF4DB37E080D9 FOREIGN KEY (banque_id) REFERENCES myfinances_banques (id)');
        $this->addSql('ALTER TABLE myfinances_comptes ADD CONSTRAINT FK_D6AF4DBA10273AA FOREIGN KEY (titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE myfinances_comptes ADD CONSTRAINT FK_D6AF4DBD253699B FOREIGN KEY (cotitulaire_id) REFERENCES mycontacts_personnes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE myfinances_comptes');
    }
}
