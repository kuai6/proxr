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

        $command = new ContactClosure();
        $command->setAdapter(new \Application\Command\Adapter\Socket());
        $command->getAdapter()->connect('127.0.0.1', 12101);

        while ($i < 100) {
            $statuses = $command->getAllStatuses();
            $i ++;
        }
        $command->getAdapter()->close();

        $delta = microtime(true) - $start;
        print_r($delta/1000);
    }
}
