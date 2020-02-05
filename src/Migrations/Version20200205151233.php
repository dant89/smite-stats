<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205151233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE match_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, child_item_id INT UNSIGNED DEFAULT NULL, root_item_id INT UNSIGNED DEFAULT NULL, active INT UNSIGNED DEFAULT 0 NOT NULL, item_name VARCHAR(255) DEFAULT NULL, icon_id INT UNSIGNED DEFAULT NULL, price INT UNSIGNED DEFAULT NULL, tier INT UNSIGNED DEFAULT NULL, starting_item INT UNSIGNED DEFAULT 0, short_description VARCHAR(255) DEFAULT NULL, icon_url VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, restricted_roles VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX unique_match_item (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE match_item');
    }
}
