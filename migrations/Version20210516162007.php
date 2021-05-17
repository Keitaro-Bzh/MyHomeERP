<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516162007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE myfinances_modesPaiement (id INT AUTO_INCREMENT NOT NULL, titulaire_id INT NOT NULL, co_titulaire_id INT DEFAULT NULL, compte_id INT NOT NULL, type_paiement VARCHAR(50) NOT NULL, numero_carte INT DEFAULT NULL, cheque_numero_debut INT DEFAULT NULL, cheque_numero_fin INT DEFAULT NULL, date_creation DATETIME DEFAULT NULL, id_creation INT DEFAULT NULL, date_modification DATETIME DEFAULT NULL, id_modification INT DEFAULT NULL, INDEX IDX_648289FEA10273AA (titulaire_id), INDEX IDX_648289FEAAF44023 (co_titulaire_id), INDEX IDX_648289FEF2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE myfinances_sousCategories (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(50) NOT NULL, date_creation DATETIME DEFAULT NULL, id_creation INT DEFAULT NULL, date_modification DATETIME DEFAULT NULL, id_modification INT DEFAULT NULL, INDEX IDX_33AAFF24BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE myfinances_typesCompte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, abrege VARCHAR(5) NOT NULL, retrait_ok TINYINT(1) DEFAULT NULL, depot_cheque_ok TINYINT(1) DEFAULT NULL, carte_ok TINYINT(1) DEFAULT NULL, autre_mode_paiement_ok TINYINT(1) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, id_creation INT DEFAULT NULL, date_modification DATETIME DEFAULT NULL, id_modification INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE myfinances_modesPaiement ADD CONSTRAINT FK_648289FEA10273AA FOREIGN KEY (titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE myfinances_modesPaiement ADD CONSTRAINT FK_648289FEAAF44023 FOREIGN KEY (co_titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE myfinances_modesPaiement ADD CONSTRAINT FK_648289FEF2C56620 FOREIGN KEY (compte_id) REFERENCES myfinances_comptes (id)');
        $this->addSql('ALTER TABLE myfinances_sousCategories ADD CONSTRAINT FK_33AAFF24BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('DROP TABLE mode_paiement');
        $this->addSql('DROP TABLE sous_categorie');
        $this->addSql('DROP TABLE type_compte');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mode_paiement (id INT AUTO_INCREMENT NOT NULL, titulaire_id INT NOT NULL, co_titulaire_id INT DEFAULT NULL, compte_id INT NOT NULL, type_paiement VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, numero_carte INT DEFAULT NULL, cheque_numero_debut INT DEFAULT NULL, cheque_numero_fin INT DEFAULT NULL, INDEX IDX_B2BB0E85AAF44023 (co_titulaire_id), INDEX IDX_B2BB0E85F2C56620 (compte_id), INDEX IDX_B2BB0E85A10273AA (titulaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sous_categorie (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_52743D7BBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_compte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, abrege VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, retrait_ok TINYINT(1) DEFAULT NULL, depot_cheque_ok TINYINT(1) DEFAULT NULL, carte_ok TINYINT(1) DEFAULT NULL, autre_mode_paiement_ok TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85A10273AA FOREIGN KEY (titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85AAF44023 FOREIGN KEY (co_titulaire_id) REFERENCES mycontacts_personnes (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85F2C56620 FOREIGN KEY (compte_id) REFERENCES myfinances_comptes (id)');
        $this->addSql('ALTER TABLE sous_categorie ADD CONSTRAINT FK_52743D7BBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('DROP TABLE myfinances_modesPaiement');
        $this->addSql('DROP TABLE myfinances_sousCategories');
        $this->addSql('DROP TABLE myfinances_typesCompte');
    }
}
