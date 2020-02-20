<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220122302 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE player_achievement (id INT UNSIGNED AUTO_INCREMENT NOT NULL, smite_player_id INT DEFAULT NULL, assists INT UNSIGNED NOT NULL, assisted_kills INT UNSIGNED NOT NULL, camps_cleared INT UNSIGNED NOT NULL, deaths INT UNSIGNED NOT NULL, divine_spree INT UNSIGNED NOT NULL, double_kills INT UNSIGNED NOT NULL, fire_giant_kills INT UNSIGNED NOT NULL, first_bloods INT UNSIGNED NOT NULL, god_like_spree INT UNSIGNED NOT NULL, gold_fury_kills INT UNSIGNED NOT NULL, immortal_spree INT UNSIGNED NOT NULL, killing_spree INT UNSIGNED NOT NULL, minion_kills INT UNSIGNED NOT NULL, penta_kills INT UNSIGNED NOT NULL, phoenix_kills INT UNSIGNED NOT NULL, player_kills INT UNSIGNED NOT NULL, quadra_kills INT UNSIGNED NOT NULL, rampage_spree INT UNSIGNED NOT NULL, shutdown_spree INT UNSIGNED NOT NULL, siege_juggernaut_kills INT UNSIGNED NOT NULL, tower_kills INT UNSIGNED NOT NULL, triple_kills INT UNSIGNED NOT NULL, unstoppable_spree INT UNSIGNED NOT NULL, wild_juggernaut_kills INT UNSIGNED NOT NULL, UNIQUE INDEX unique_player_achievement (smite_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_achievement ADD CONSTRAINT FK_CE449664438C20B3 FOREIGN KEY (smite_player_id) REFERENCES player (smite_player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE player_achievement');
    }
}
