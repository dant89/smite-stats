<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191204003811 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_item CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE item_name item_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE match_player_ability CHANGE ability_id ability_id INT UNSIGNED DEFAULT NULL, CHANGE ability_name ability_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE match_player_ban CHANGE ban_id ban_id INT UNSIGNED DEFAULT NULL, CHANGE ban_name ban_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_ability CHANGE ability_id ability_id INT UNSIGNED NOT NULL, CHANGE ability_name ability_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE match_player_ban CHANGE ban_id ban_id INT UNSIGNED NOT NULL, CHANGE ban_name ban_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE match_player_item CHANGE item_id item_id INT UNSIGNED NOT NULL, CHANGE item_name item_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
