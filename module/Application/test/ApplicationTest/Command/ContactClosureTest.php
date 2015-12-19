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
        $command->getAdapter()->connect($config['contactClosure']['host'], $config['contactClosure']['port']);

        $cnt = isset($config['count']) ? $config['count'] : 5;
        while ($i < $cnt) {
            $command->getAllStatuses();
            $i ++;
        }
        $command->getAdapter()->close();

        $delta = microtime(true) - $start;
        print_r(sprintf("Total time: %f8\n", $delta));
        print_r(sprintf("Requests per sec: %f8\n", $cnt/$delta));
    }
}
