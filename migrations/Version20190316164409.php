<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316164409 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE activity ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD nodes VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD links VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE activity DROP name');
        $this->addSql('ALTER TABLE activity DROP description');
        $this->addSql('ALTER TABLE activity DROP nodes');
        $this->addSql('ALTER TABLE activity DROP links');
    }
}
