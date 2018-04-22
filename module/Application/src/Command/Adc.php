<?php

namespace Application\Command;

use Application\Entity\Bank;

/**
 * Class Adc
 * @package Application\Command
 */
class Adc extends AbstractCommand
{
    public function get(Bank $bank, $bit, $value)
    {
        return sprintf("%s%s", $bit, $value);
    }
}