<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191204002846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_item DROP FOREIGN KEY FK_8F980405BF396750');
        $this->addSql('ALTER TABLE match_player_item ADD match_player_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE match_player_item ADD CONSTRAINT FK_8F9804058057B974 FOREIGN KEY (match_player_id) REFERENCES match_player (id)');
        $this->addSql('CREATE INDEX IDX_8F9804058057B974 ON match_player_item (match_player_id)');
        $this->addSql('ALTER TABLE match_player_ability DROP FOREIGN KEY FK_DD926544BF396750');
        $this->addSql('ALTER TABLE match_player_ability ADD match_player_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE match_player_ability ADD CONSTRAINT FK_DD9265448057B974 FOREIGN KEY (match_player_id) REFERENCES match_player (id)');
        $this->addSql('CREATE INDEX IDX_DD9265448057B974 ON match_player_ability (match_player_id)');
        $this->addSql('ALTER TABLE match_player_ban DROP FOREIGN KEY FK_5EC25AEABF396750');
        $this->addSql('ALTER TABLE match_player_ban ADD match_player_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE match_player_ban ADD CONSTRAINT FK_5EC25AEA8057B974 FOREIGN KEY (match_player_id) REFERENCES match_player (id)');
        $this->addSql('CREATE INDEX IDX_5EC25AEA8057B974 ON match_player_ban (match_player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_ability DROP FOREIGN KEY FK_DD9265448057B974');
        $this->addSql('DROP INDEX IDX_DD9265448057B974 ON match_player_ability');
        $this->addSql('ALTER TABLE match_player_ability DROP match_player_id');
        $this->addSql('ALTER TABLE match_player_ability ADD CONSTRAINT FK_DD926544BF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
        $this->addSql('ALTER TABLE match_player_ban DROP FOREIGN KEY FK_5EC25AEA8057B974');
        $this->addSql('DROP INDEX IDX_5EC25AEA8057B974 ON match_player_ban');
        $this->addSql('ALTER TABLE match_player_ban DROP match_player_id');
        $this->addSql('ALTER TABLE match_player_ban ADD CONSTRAINT FK_5EC25AEABF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
        $this->addSql('ALTER TABLE match_player_item DROP FOREIGN KEY FK_8F9804058057B974');
        $this->addSql('DROP INDEX IDX_8F9804058057B974 ON match_player_item');
        $this->addSql('ALTER TABLE match_player_item DROP match_player_id');
        $this->addSql('ALTER TABLE match_player_item ADD CONSTRAINT FK_8F980405BF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
    }
}
