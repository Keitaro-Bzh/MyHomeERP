<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516144147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mode_paiement (id INT AUTO_INCREMENT NOT NULL, titulaire_id INT NOT NULL, co_titulaire_id INT DEFAULT NULL, compte_id INT NOT NULL, type_paiement VARCHAR(50) NOT NULL, numero_carte INT DEFAULT NULL, cheque_numero_debut INT DEFAULT NULL, cheque_numero_fin INT DEFAULT NULL, INDEX IDX_B2BB0E85A10273AA (titulaire_id), INDEX IDX_B2BB0E85AAF44023 (co_titulaire_id), INDEX IDX_B2BB0E85F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_compte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, abrege VARCHAR(5) NOT NULL, retrait_ok TINYINT(1) DEFAULT NULL, depot_cheque_ok TINYINT(1) DEFAULT NULL, carte_ok TINYINT(1) DEFAULT NULL, autre_mode_paiement_ok TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85A10273AA FOREIGN KEY (titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85AAF44023 FOREIGN KEY (co_titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85F2C56620 FOREIGN KEY (compte_id) REFERENCES myfinances_comptes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mode_paiement');
        $this->addSql('DROP TABLE type_compte');
    }
}
