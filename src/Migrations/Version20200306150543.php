<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200306150543 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE god_skin (id INT UNSIGNED AUTO_INCREMENT NOT NULL, god_id INT UNSIGNED DEFAULT NULL, id1 INT NOT NULL, id2 INT NOT NULL, name VARCHAR(255) NOT NULL, skin_url VARCHAR(255) NOT NULL, obtainability VARCHAR(255) NOT NULL, price_favor INT DEFAULT 0 NOT NULL, price_gems INT DEFAULT 0 NOT NULL, INDEX IDX_C8E9E893921502AB (god_id), UNIQUE INDEX unique_god_skin (id1, id2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE god_skin ADD CONSTRAINT FK_C8E9E893921502AB FOREIGN KEY (god_id) REFERENCES god (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE god_skin');
    }
}
