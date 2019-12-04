<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191203235927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE match_player_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED NOT NULL, item_name VARCHAR(255) NOT NULL, item_number INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_player_ability (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ability_id INT UNSIGNED NOT NULL, ability_name VARCHAR(255) NOT NULL, ability_number INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_player (id INT UNSIGNED AUTO_INCREMENT NOT NULL, smite_match_id INT UNSIGNED NOT NULL, smite_player_id INT UNSIGNED NOT NULL, account_level INT UNSIGNED DEFAULT 0, assists INT UNSIGNED DEFAULT 0, camps_cleared INT UNSIGNED DEFAULT 0 NOT NULL, conquest_losses INT UNSIGNED DEFAULT 0 NOT NULL, conquest_points INT UNSIGNED DEFAULT 0 NOT NULL, conquest_tier INT UNSIGNED DEFAULT 0 NOT NULL, conquest_wins INT UNSIGNED DEFAULT 0 NOT NULL, damage_bot INT UNSIGNED DEFAULT 0 NOT NULL, damage_done_in_hand INT UNSIGNED DEFAULT 0 NOT NULL, damage_done_magical INT UNSIGNED DEFAULT 0 NOT NULL, damage_done_physical INT UNSIGNED DEFAULT 0 NOT NULL, damage_mitigated INT UNSIGNED DEFAULT 0 NOT NULL, damage_player INT UNSIGNED DEFAULT 0 NOT NULL, damage_taken INT UNSIGNED DEFAULT 0 NOT NULL, damage_taken_magical INT UNSIGNED DEFAULT 0 NOT NULL, damage_taken_physical INT UNSIGNED DEFAULT 0 NOT NULL, deaths INT UNSIGNED DEFAULT 0 NOT NULL, distance_traveled INT UNSIGNED DEFAULT 0 NOT NULL, duel_losses INT UNSIGNED DEFAULT 0 NOT NULL, duel_points INT UNSIGNED DEFAULT 0 NOT NULL, duel_tier INT UNSIGNED DEFAULT 0 NOT NULL, duel_wins INT UNSIGNED DEFAULT 0 NOT NULL, entry_datetime DATETIME NOT NULL, final_match_level INT UNSIGNED DEFAULT 0 NOT NULL, first_ban_side VARCHAR(255) DEFAULT NULL, god_id INT UNSIGNED DEFAULT 0 NOT NULL, gold_earned INT UNSIGNED DEFAULT 0 NOT NULL, gold_per_minute INT UNSIGNED DEFAULT 0 NOT NULL, healing INT UNSIGNED DEFAULT 0 NOT NULL, healing_bot INT UNSIGNED DEFAULT 0 NOT NULL, healing_player_self INT UNSIGNED DEFAULT 0 NOT NULL, joust_losses INT UNSIGNED DEFAULT 0 NOT NULL, joust_points INT UNSIGNED DEFAULT 0 NOT NULL, joust_tier INT UNSIGNED DEFAULT 0 NOT NULL, joust_wins INT UNSIGNED DEFAULT 0 NOT NULL, killing_spree INT UNSIGNED DEFAULT 0 NOT NULL, kills_bot INT UNSIGNED DEFAULT 0 NOT NULL, kills_double INT UNSIGNED DEFAULT 0 NOT NULL, kills_fire_giant INT UNSIGNED DEFAULT 0 NOT NULL, kills_first_blood INT UNSIGNED DEFAULT 0 NOT NULL, kills_gold_fury INT UNSIGNED DEFAULT 0 NOT NULL, kills_penta INT UNSIGNED DEFAULT 0 NOT NULL, kills_phoenix INT UNSIGNED DEFAULT 0 NOT NULL, kills_player INT UNSIGNED DEFAULT 0 NOT NULL, kills_quadra INT UNSIGNED DEFAULT 0 NOT NULL, kills_siege_juggernaut INT UNSIGNED DEFAULT 0 NOT NULL, kills_single INT UNSIGNED DEFAULT 0 NOT NULL, kills_triple INT UNSIGNED DEFAULT 0 NOT NULL, kills_wild_juggernaut INT UNSIGNED DEFAULT 0 NOT NULL, map_game VARCHAR(255) DEFAULT NULL, mastery_level INT UNSIGNED DEFAULT 0 NOT NULL, match_duration INT UNSIGNED DEFAULT 0 NOT NULL, minutes INT UNSIGNED DEFAULT 0 NOT NULL, multi_kill_max INT UNSIGNED DEFAULT 0 NOT NULL, objective_assists INT UNSIGNED DEFAULT 0 NOT NULL, party_id INT UNSIGNED DEFAULT 0 NOT NULL, rank_stat_conquest DOUBLE PRECISION UNSIGNED DEFAULT \'0\' NOT NULL, rank_stat_duel DOUBLE PRECISION UNSIGNED DEFAULT \'0\' NOT NULL, rank_stat_joust DOUBLE PRECISION UNSIGNED DEFAULT \'0\' NOT NULL, reference_name VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, skin VARCHAR(255) DEFAULT NULL, skin_id INT UNSIGNED DEFAULT 0 NOT NULL, structure_damage INT UNSIGNED DEFAULT 0 NOT NULL, surrendered INT UNSIGNED DEFAULT 0 NOT NULL, task_force INT UNSIGNED DEFAULT 0 NOT NULL, team1score INT UNSIGNED DEFAULT 0 NOT NULL, team2score INT UNSIGNED DEFAULT 0 NOT NULL, team_id INT UNSIGNED DEFAULT 0 NOT NULL, team_name VARCHAR(255) DEFAULT NULL, time_in_match_seconds INT UNSIGNED DEFAULT 0 NOT NULL, towers_destroyed INT UNSIGNED DEFAULT 0 NOT NULL, wards_placed INT UNSIGNED DEFAULT 0 NOT NULL, win_status VARCHAR(255) DEFAULT NULL, winning_task_force INT UNSIGNED DEFAULT 0 NOT NULL, match_queue_id INT UNSIGNED DEFAULT 0 NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX unique_smite_id (smite_match_id, smite_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_player_ban (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ban_id INT UNSIGNED NOT NULL, ban_name VARCHAR(255) NOT NULL, ban_number INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE match_player_item ADD CONSTRAINT FK_8F980405BF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
        $this->addSql('ALTER TABLE match_player_ability ADD CONSTRAINT FK_DD926544BF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
        $this->addSql('ALTER TABLE match_player_ban ADD CONSTRAINT FK_5EC25AEABF396750 FOREIGN KEY (id) REFERENCES match_player (smite_match_id)');
        $this->addSql('DROP TABLE `match`');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player_item DROP FOREIGN KEY FK_8F980405BF396750');
        $this->addSql('ALTER TABLE match_player_ability DROP FOREIGN KEY FK_DD926544BF396750');
        $this->addSql('ALTER TABLE match_player_ban DROP FOREIGN KEY FK_5EC25AEABF396750');
        $this->addSql('CREATE TABLE `match` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE match_player_item');
        $this->addSql('DROP TABLE match_player_ability');
        $this->addSql('DROP TABLE match_player');
        $this->addSql('DROP TABLE match_player_ban');
    }
}
