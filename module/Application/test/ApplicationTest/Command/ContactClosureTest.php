<?php

namespace ApplicationTest\Command;

use Application\Command\ContactClosure;

/**
 * Class ContactClosureTest
 * @package ApplicationTest\Command
 */
class ContactClosureTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
    }

    public function testAdapter()
    {
        $i = 0;
        $start = microtime(true);

        $config = include __DIR__ .'/../../config/device.config.php';
        $command = new ContactClosure();
        $command->setAdapter(new \Application\Command\Adapter\Socket());
        $command->getAdapter()->connect($config['host'], $config['port']);

        while ($i < 100) {
            $statuses = $command->getAllStatuses();
            $i ++;
        }
        $command->getAdapter()->close();

        $delta = microtime(true) - $start;
        print_r($delta/1000);
    }
}
