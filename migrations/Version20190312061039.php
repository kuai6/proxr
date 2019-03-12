<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190312061039 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE eventLog (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, dateTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bit0 INT DEFAULT NULL, bit0_direction VARCHAR(255) DEFAULT NULL, bit1 INT DEFAULT NULL, bit1_direction VARCHAR(255) DEFAULT NULL, bit2 INT DEFAULT NULL, bit2_direction VARCHAR(255) DEFAULT NULL, bit3 INT DEFAULT NULL, bit3_direction VARCHAR(255) DEFAULT NULL, bit4 INT DEFAULT NULL, bit4_direction VARCHAR(255) DEFAULT NULL, bit5 INT DEFAULT NULL, bit5_direction VARCHAR(255) DEFAULT NULL, bit6 INT DEFAULT NULL, bit6_direction VARCHAR(255) DEFAULT NULL, bit7 INT DEFAULT NULL, bit7_direction VARCHAR(255) DEFAULT NULL, deviceId INT NOT NULL, bankId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A0C4039EADBFE9A1 ON eventLog (deviceId)');
        $this->addSql('CREATE INDEX IDX_A0C4039EA4B3A4F3 ON eventLog (bankId)');
        $this->addSql('CREATE TABLE activity (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, metadata TEXT DEFAULT NULL, event VARCHAR(255) DEFAULT NULL, bit VARCHAR(255) DEFAULT NULL, on_event VARCHAR(255) DEFAULT NULL, statusId INT NOT NULL, deviceId INT NOT NULL, bankId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC74095AF112F078 ON activity (statusId)');
        $this->addSql('CREATE INDEX IDX_AC74095AADBFE9A1 ON activity (deviceId)');
        $this->addSql('CREATE INDEX IDX_AC74095AA4B3A4F3 ON activity (bankId)');
        $this->addSql('CREATE TABLE bank (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, name SMALLINT DEFAULT NULL, bit0 INT DEFAULT NULL, bit1 INT DEFAULT NULL, bit2 INT DEFAULT NULL, bit3 INT DEFAULT NULL, bit4 INT DEFAULT NULL, bit5 INT DEFAULT NULL, bit6 INT DEFAULT NULL, bit7 INT DEFAULT NULL, deviceId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D860BF7AADBFE9A1 ON bank (deviceId)');
        $this->addSql('CREATE TABLE status (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE device (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, port VARCHAR(255) DEFAULT NULL, last_ping TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, statusId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92FB68EF112F078 ON device (statusId)');
        $this->addSql('ALTER TABLE eventLog ADD CONSTRAINT FK_A0C4039EADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eventLog ADD CONSTRAINT FK_A0C4039EA4B3A4F3 FOREIGN KEY (bankId) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AF112F078 FOREIGN KEY (statusId) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA4B3A4F3 FOREIGN KEY (bankId) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EF112F078 FOREIGN KEY (statusId) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE eventLog DROP CONSTRAINT FK_A0C4039EA4B3A4F3');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AA4B3A4F3');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AF112F078');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68EF112F078');
        $this->addSql('ALTER TABLE eventLog DROP CONSTRAINT FK_A0C4039EADBFE9A1');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AADBFE9A1');
        $this->addSql('ALTER TABLE bank DROP CONSTRAINT FK_D860BF7AADBFE9A1');
        $this->addSql('DROP TABLE eventLog');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE device');
    }
}
