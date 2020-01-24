<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124112730 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE player_god (id INT UNSIGNED AUTO_INCREMENT NOT NULL, smite_player_id INT DEFAULT NULL, god_id INT UNSIGNED DEFAULT NULL, assists INT UNSIGNED NOT NULL, deaths INT UNSIGNED NOT NULL, kills INT UNSIGNED NOT NULL, losses INT UNSIGNED NOT NULL, minion_kills INT UNSIGNED NOT NULL, rank INT UNSIGNED NOT NULL, wins INT UNSIGNED NOT NULL, worshippers INT UNSIGNED NOT NULL, INDEX IDX_674297BD438C20B3 (smite_player_id), INDEX IDX_674297BD921502AB (god_id), UNIQUE INDEX unique_player_god (smite_player_id, god_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_god ADD CONSTRAINT FK_674297BD438C20B3 FOREIGN KEY (smite_player_id) REFERENCES player (smite_player_id)');
        $this->addSql('ALTER TABLE player_god ADD CONSTRAINT FK_674297BD921502AB FOREIGN KEY (god_id) REFERENCES god (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE player_god');
    }
}
