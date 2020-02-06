<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205163440 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_ability DROP ability_name');
        $this->addSql('ALTER TABLE match_player_ability ADD CONSTRAINT FK_DD9265448016D8B2 FOREIGN KEY (ability_id) REFERENCES match_item (item_id)');
        $this->addSql('CREATE INDEX IDX_DD9265448016D8B2 ON match_player_ability (ability_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_ability DROP FOREIGN KEY FK_DD9265448016D8B2');
        $this->addSql('DROP INDEX IDX_DD9265448016D8B2 ON match_player_ability');
        $this->addSql('ALTER TABLE match_player_ability ADD ability_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
