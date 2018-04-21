<?php

namespace Application\Command;

use Application\Command\Adapter\AdapterInterface;

/**
 * Class AbstractCommand
 * @package Application\Command
 */
class AbstractCommand implements CommandInterface
{
    const CONTROL_COMMAND = 'DATA';

    /**
     * Command sequnce, e.q. [0=>254, 1=>2] turns off relay 2 in bank 1
     * @var array;
     */
    protected $sequence = [self::CONTROL_COMMAND];
}
