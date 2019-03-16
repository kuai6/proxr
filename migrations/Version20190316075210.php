<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316075210 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE periphery_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE periphery_unit (id SERIAL NOT NULL, type_id INT NOT NULL, device_id INT NOT NULL, bank_id INT NOT NULL, bit INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527F0ABCC54C8C93 ON periphery_unit (type_id)');
        $this->addSql('CREATE INDEX IDX_527F0ABC94A4C7D4 ON periphery_unit (device_id)');
        $this->addSql('CREATE INDEX IDX_527F0ABC11C8FB41 ON periphery_unit (bank_id)');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABCC54C8C93 FOREIGN KEY (type_id) REFERENCES periphery_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABC94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABC11C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE periphery_unit DROP CONSTRAINT FK_527F0ABCC54C8C93');
        $this->addSql('DROP TABLE periphery_type');
        $this->addSql('DROP TABLE periphery_unit');
    }
}
