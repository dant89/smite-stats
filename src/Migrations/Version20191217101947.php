<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191217101947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player CHANGE smite_player_id smite_player_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_39768364438C20B3 ON match_player (smite_player_id)');
        $this->addSql('ALTER TABLE player MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('DROP INDEX unique_player_id ON player');
        $this->addSql('ALTER TABLE player DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE player DROP id, CHANGE smite_player_id smite_player_id INT NOT NULL');
        $this->addSql('ALTER TABLE player ADD PRIMARY KEY (smite_player_id)');
        $this->addSql('ALTER TABLE match_player ADD CONSTRAINT FK_39768364438C20B3 FOREIGN KEY (smite_player_id) REFERENCES player (smite_player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_player DROP FOREIGN KEY FK_39768364438C20B3');
        $this->addSql('DROP INDEX IDX_39768364438C20B3 ON match_player');
        $this->addSql('ALTER TABLE match_player CHANGE smite_player_id smite_player_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE player ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE smite_player_id smite_player_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_player_id ON player (smite_player_id)');
    }
}
