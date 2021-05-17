<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210515185027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comptes');
        $this->addSql('ALTER TABLE mycontacts_societes ADD id_creation INT DEFAULT NULL, ADD id_modification INT DEFAULT NULL');
        $this->addSql('ALTER TABLE myerp_users ADD id_creation INT DEFAULT NULL, ADD id_modification INT DEFAULT NULL');
        $this->addSql('ALTER TABLE myfinances_banques ADD date_creation DATETIME DEFAULT NULL, ADD id_creation INT DEFAULT NULL, ADD date_modification DATETIME DEFAULT NULL, ADD id_modification INT DEFAULT NULL');
        $this->addSql('ALTER TABLE myfinances_comptes ADD date_creation DATETIME DEFAULT NULL, ADD id_creation INT DEFAULT NULL, ADD date_modification DATETIME DEFAULT NULL, ADD id_modification INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comptes (id INT AUTO_INCREMENT NOT NULL, type_compte VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE mycontacts_societes DROP id_creation, DROP id_modification');
        $this->addSql('ALTER TABLE myerp_users DROP id_creation, DROP id_modification');
        $this->addSql('ALTER TABLE myfinances_banques DROP date_creation, DROP id_creation, DROP date_modification, DROP id_modification');
        $this->addSql('ALTER TABLE myfinances_comptes DROP date_creation, DROP id_creation, DROP date_modification, DROP id_modification');
    }
}
