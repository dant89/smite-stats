<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205155935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_item MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE match_item DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE match_item DROP id, CHANGE item_id item_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE match_item ADD PRIMARY KEY (item_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE match_item ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
