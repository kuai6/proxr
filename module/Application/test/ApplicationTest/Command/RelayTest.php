<?php

namespace ApplicationTest\Command;

use Application\Command\Relay;

class RelayTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
    }

    public function testAdapter()
    {
        $config = include __DIR__ .'/../../config/device.config.php';
        $command = new Relay();
        $command->setAdapter(new \Application\Command\Adapter\Socket());
        $command->getAdapter()->connect($config['relay']['host'], $config['relay']['port']);

        $bank = new \Application\Entity\Bank\Relay();
        $bank->setName(1);

        for ($i = 0; $i < 8; $i++) {
            $result = $command->on($bank, $i);
            static::assertTrue($result);
            $status = $command->status($bank, $i);
            static::assertEquals(1, $status);
        }

        for ($i = 0; $i < 8; $i++) {
            $result = $command->off($bank, $i);
            static::assertTrue($result);
            $status = $command->status($bank, $i);
            static::assertEquals(0, $status);
        }

        for ($i = 0; $i < 8; $i++) {
            $result = $command->toggle($bank, $i);
            static::assertTrue($result);
        }

        for ($i = 0; $i < 8; $i++) {
            $result = $command->toggle($bank, $i);
            static::assertTrue($result);
        }
    }
}
