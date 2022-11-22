<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121210815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C216D1BCE6F');
        $this->addSql('DROP INDEX IDX_4B98C216D1BCE6F ON groupe');
        $this->addSql('ALTER TABLE groupe DROP group_admin_id');
        $this->addSql('ALTER TABLE user ADD managed_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649208E508C FOREIGN KEY (managed_group_id) REFERENCES groupe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649208E508C ON user (managed_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe ADD group_admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C216D1BCE6F FOREIGN KEY (group_admin_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4B98C216D1BCE6F ON groupe (group_admin_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649208E508C');
        $this->addSql('DROP INDEX UNIQ_8D93D649208E508C ON user');
        $this->addSql('ALTER TABLE user DROP managed_group_id');
    }
}
