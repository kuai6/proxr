<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190316214948 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE periphery_unit (id SERIAL NOT NULL, type_id INT NOT NULL, device_id INT NOT NULL, bank_id INT NOT NULL, bit INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527F0ABCC54C8C93 ON periphery_unit (type_id)');
        $this->addSql('CREATE INDEX IDX_527F0ABC94A4C7D4 ON periphery_unit (device_id)');
        $this->addSql('CREATE INDEX IDX_527F0ABC11C8FB41 ON periphery_unit (bank_id)');
        $this->addSql('CREATE TABLE periphery_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, inputs INT NOT NULL, outputs INT NOT NULL, bankType VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE activity (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, metadata TEXT DEFAULT NULL, event VARCHAR(255) DEFAULT NULL, bit VARCHAR(255) DEFAULT NULL, on_event VARCHAR(255) DEFAULT NULL, deviceId INT NOT NULL, bankId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC74095AADBFE9A1 ON activity (deviceId)');
        $this->addSql('CREATE INDEX IDX_AC74095AA4B3A4F3 ON activity (bankId)');
        $this->addSql('CREATE TABLE device (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, port VARCHAR(255) DEFAULT NULL, last_ping TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE bank (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, name SMALLINT DEFAULT NULL, available_bits_count SMALLINT DEFAULT NULL, bit0 INT DEFAULT NULL, bit1 INT DEFAULT NULL, bit2 INT DEFAULT NULL, bit3 INT DEFAULT NULL, bit4 INT DEFAULT NULL, bit5 INT DEFAULT NULL, bit6 INT DEFAULT NULL, bit7 INT DEFAULT NULL, deviceId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D860BF7AADBFE9A1 ON bank (deviceId)');
        $this->addSql('CREATE TABLE eventLog (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, dateTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bit0 INT DEFAULT NULL, bit0_direction VARCHAR(255) DEFAULT NULL, bit1 INT DEFAULT NULL, bit1_direction VARCHAR(255) DEFAULT NULL, bit2 INT DEFAULT NULL, bit2_direction VARCHAR(255) DEFAULT NULL, bit3 INT DEFAULT NULL, bit3_direction VARCHAR(255) DEFAULT NULL, bit4 INT DEFAULT NULL, bit4_direction VARCHAR(255) DEFAULT NULL, bit5 INT DEFAULT NULL, bit5_direction VARCHAR(255) DEFAULT NULL, bit6 INT DEFAULT NULL, bit6_direction VARCHAR(255) DEFAULT NULL, bit7 INT DEFAULT NULL, bit7_direction VARCHAR(255) DEFAULT NULL, deviceId INT NOT NULL, bankId INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A0C4039EADBFE9A1 ON eventLog (deviceId)');
        $this->addSql('CREATE INDEX IDX_A0C4039EA4B3A4F3 ON eventLog (bankId)');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABCC54C8C93 FOREIGN KEY (type_id) REFERENCES periphery_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABC94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periphery_unit ADD CONSTRAINT FK_527F0ABC11C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA4B3A4F3 FOREIGN KEY (bankId) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eventLog ADD CONSTRAINT FK_A0C4039EADBFE9A1 FOREIGN KEY (deviceId) REFERENCES device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE eventLog ADD CONSTRAINT FK_A0C4039EA4B3A4F3 FOREIGN KEY (bankId) REFERENCES bank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE periphery_unit DROP CONSTRAINT FK_527F0ABCC54C8C93');
        $this->addSql('ALTER TABLE periphery_unit DROP CONSTRAINT FK_527F0ABC94A4C7D4');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AADBFE9A1');
        $this->addSql('ALTER TABLE bank DROP CONSTRAINT FK_D860BF7AADBFE9A1');
        $this->addSql('ALTER TABLE eventLog DROP CONSTRAINT FK_A0C4039EADBFE9A1');
        $this->addSql('ALTER TABLE periphery_unit DROP CONSTRAINT FK_527F0ABC11C8FB41');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AA4B3A4F3');
        $this->addSql('ALTER TABLE eventLog DROP CONSTRAINT FK_A0C4039EA4B3A4F3');
        $this->addSql('DROP TABLE periphery_unit');
        $this->addSql('DROP TABLE periphery_type');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE eventLog');
    }
}
