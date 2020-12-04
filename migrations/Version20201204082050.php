<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201204082050 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history ADD sms_id INT NOT NULL');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BBD5C7E60 FOREIGN KEY (sms_id) REFERENCES sms (id)');
        $this->addSql('CREATE INDEX IDX_27BA704BBD5C7E60 ON history (sms_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BBD5C7E60');
        $this->addSql('DROP INDEX IDX_27BA704BBD5C7E60 ON history');
        $this->addSql('ALTER TABLE history DROP sms_id');
    }
}
