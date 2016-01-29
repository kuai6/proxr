<?php

namespace Application\Command;

use Application\Command\Adapter\AdapterInterface;

/**
 * Class AbstractCommand
 * @package Application\Command
 */
class AbstractCommand implements CommandInterface
{
    const CONTROL_COMMAND = 254;

    /**
     * Command sequnce, e.q. [0=>254, 1=>2] turns off relay 2 in bank 1
     * @var array;
     */
    protected $sequence = [self::CONTROL_COMMAND];

    /** @var  AdapterInterface */
    protected $adapter;

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     * @return AbstractCommand
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }


    public function execute()
    {
    }
}
