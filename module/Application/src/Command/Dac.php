<?php

namespace Application\Command;

use Application\Entity\Bank;

/**
 * Class Dac
 * @package Application\Command
 */
class Dac extends AbstractCommand
{
    public function set(Bank $bank, $bit, $value)
    {
        return sprintf("%s%s", $bit, $value);
    }
}