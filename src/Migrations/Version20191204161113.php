<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191204161113 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX unique_match_player_id ON match_player');
        $this->addSql('ALTER TABLE match_player CHANGE god_id god_id INT UNSIGNED NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_match_player_id ON match_player (smite_match_id, smite_player_id, god_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX unique_match_player_id ON match_player');
        $this->addSql('ALTER TABLE match_player CHANGE god_id god_id INT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_match_player_id ON match_player (smite_match_id, smite_player_id)');
    }
}
