<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121214333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE clan (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, date_entered DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE god (id INT UNSIGNED AUTO_INCREMENT NOT NULL, smite_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, attack_speed DOUBLE PRECISION DEFAULT NULL, attack_speed_per_level DOUBLE PRECISION DEFAULT NULL, hp5per_level DOUBLE PRECISION DEFAULT NULL, health DOUBLE PRECISION DEFAULT NULL, health_per_five DOUBLE PRECISION DEFAULT NULL, health_per_level DOUBLE PRECISION DEFAULT NULL, mp5per_level DOUBLE PRECISION DEFAULT NULL, magic_protection DOUBLE PRECISION DEFAULT NULL, magic_protection_per_level DOUBLE PRECISION DEFAULT NULL, magical_power DOUBLE PRECISION DEFAULT NULL, magical_power_per_level DOUBLE PRECISION DEFAULT NULL, mana DOUBLE PRECISION DEFAULT NULL, mana_per_five DOUBLE PRECISION DEFAULT NULL, mana_per_level DOUBLE PRECISION DEFAULT NULL, physical_power DOUBLE PRECISION DEFAULT NULL, physical_power_per_level DOUBLE PRECISION DEFAULT NULL, physical_protection DOUBLE PRECISION DEFAULT NULL, physical_protection_per_level DOUBLE PRECISION DEFAULT NULL, speed DOUBLE PRECISION DEFAULT NULL, pros VARCHAR(255) DEFAULT NULL, cons VARCHAR(255) DEFAULT NULL, on_free_rotation VARCHAR(255) DEFAULT NULL, pantheon VARCHAR(255) DEFAULT NULL, roles VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, lore LONGTEXT DEFAULT NULL, card_url VARCHAR(255) DEFAULT NULL, icon_url VARCHAR(255) DEFAULT NULL, date_created DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, date_updated DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, UNIQUE INDEX unique_smite_id (smite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `match` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE god_ability (id INT UNSIGNED AUTO_INCREMENT NOT NULL, god_id INT UNSIGNED DEFAULT NULL, ability_number INT UNSIGNED NOT NULL, ability_id INT UNSIGNED NOT NULL, summary LONGTEXT DEFAULT NULL, url LONGTEXT DEFAULT NULL, cooldown LONGTEXT DEFAULT NULL, cost LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_17AE6D2A921502AB (god_id), UNIQUE INDEX unique_ability_id (ability_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_call (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, response_status INT DEFAULT NULL, cached INT NOT NULL, date_created DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE god_ability ADD CONSTRAINT FK_17AE6D2A921502AB FOREIGN KEY (god_id) REFERENCES god (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE god_ability DROP FOREIGN KEY FK_17AE6D2A921502AB');
        $this->addSql('DROP TABLE clan');
        $this->addSql('DROP TABLE god');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE god_ability');
        $this->addSql('DROP TABLE api_call');
    }
}
