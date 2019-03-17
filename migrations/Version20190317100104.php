<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190317100104 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO public.periphery_type (id, name, description, icon, inputs, outputs, banktype) VALUES (1, 'switch', 'regular button', null, 0, 1, 'contactClosure')");
        $this->addSql("INSERT INTO public.periphery_type (id, name, description, icon, inputs, outputs, banktype) VALUES (2, 'lamp', 'regular lamp', null, 1, 0, 'relay')");
        $this->addSql("INSERT INTO public.periphery_type (id, name, description, icon, inputs, outputs, banktype) VALUES (3, 'temp', 'temperature sensor', null, 0, 1, 'adc')");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("DELETE FROM public.periphery_type WHERE id IN (1,2,3)");
    }
}
