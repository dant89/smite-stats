<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200303195533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE match_item_effect (id INT UNSIGNED AUTO_INCREMENT NOT NULL, match_item_id INT UNSIGNED DEFAULT NULL, description VARCHAR(50) NOT NULL, value VARCHAR(20) NOT NULL, INDEX IDX_200951565C90D31 (match_item_id), UNIQUE INDEX unique_match_item_effect (match_item_id, description, value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE match_item_effect ADD CONSTRAINT FK_200951565C90D31 FOREIGN KEY (match_item_id) REFERENCES match_item (item_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE match_item_effect');
    }
}
