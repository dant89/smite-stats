<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191204004420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX unique_match_player_item ON match_player_item (item_id, item_name, match_player_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_match_player_ability ON match_player_ability (ability_id, ability_number, match_player_id)');
        $this->addSql('DROP INDEX unique_smite_id ON match_player');
        $this->addSql('CREATE UNIQUE INDEX unique_match_player_id ON match_player (smite_match_id, smite_player_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_match_player_ban ON match_player_ban (ban_id, ban_number, match_player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX unique_match_player_id ON match_player');
        $this->addSql('CREATE UNIQUE INDEX unique_smite_id ON match_player (smite_match_id, smite_player_id)');
        $this->addSql('DROP INDEX unique_match_player_ability ON match_player_ability');
        $this->addSql('DROP INDEX unique_match_player_ban ON match_player_ban');
        $this->addSql('DROP INDEX unique_match_player_item ON match_player_item');
    }
}
