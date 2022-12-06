<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206131430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE password (id INT AUTO_INCREMENT NOT NULL, groupe_id INT NOT NULL, description VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, encrypted_password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, used_login VARCHAR(255) NOT NULL, recuparation_email VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_35C246D57A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE password ADD CONSTRAINT FK_35C246D57A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE groupe ADD private_key VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD managed_group_id INT DEFAULT NULL, ADD reset_token VARCHAR(100) DEFAULT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD is_temporary_password_change TINYINT(1) NOT NULL, ADD is_key_change TINYINT(1) NOT NULL, ADD private_key VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649208E508C FOREIGN KEY (managed_group_id) REFERENCES groupe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649208E508C ON user (managed_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password DROP FOREIGN KEY FK_35C246D57A45358C');
        $this->addSql('DROP TABLE password');
        $this->addSql('ALTER TABLE groupe DROP private_key');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649208E508C');
        $this->addSql('DROP INDEX UNIQ_8D93D649208E508C ON user');
        $this->addSql('ALTER TABLE user DROP managed_group_id, DROP reset_token, DROP is_verified, DROP is_temporary_password_change, DROP is_key_change, DROP private_key, CHANGE password password VARCHAR(255) NOT NULL');
    }
}
