<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125164135 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clan ADD smite_clan_id VARCHAR(255) NOT NULL, ADD founder VARCHAR(255) DEFAULT NULL, ADD wins INT DEFAULT 0 NOT NULL, ADD losses INT DEFAULT 0 NOT NULL, ADD players INT DEFAULT 0 NOT NULL, ADD rating INT DEFAULT 0 NOT NULL, ADD tag VARCHAR(10) DEFAULT NULL, ADD date_created DATETIME NOT NULL, ADD date_updated DATETIME NOT NULL, ADD crawled INT DEFAULT 0 NOT NULL, DROP date_entered');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clan ADD date_entered DATETIME DEFAULT CURRENT_TIMESTAMP, DROP smite_clan_id, DROP founder, DROP wins, DROP losses, DROP players, DROP rating, DROP tag, DROP date_created, DROP date_updated, DROP crawled');
    }
}
