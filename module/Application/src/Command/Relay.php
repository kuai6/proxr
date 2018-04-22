<?php

namespace Application\Command;

use Application\Command\Exception\RuntimeException;
use Application\Entity\Bank;

/**
 * Class Relay
 * @package Application\Command
 */
class Relay extends AbstractCommand
{
    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws RuntimeException
     */
    public function on(Bank $bank, $bit)
    {
        if ($bank->getName() === null) {
            throw new RuntimeException('Bank name not specified');
        }
        $setBit = 'setBit' . $bit;
        if(!method_exists($bank, $setBit)) {
            throw new RuntimeException(sprintf("Method %s not exist", $setBit));
        }
        $bank->{$setBit}(1);

        return sprintf("%s%s%s", $bank->getName(), $bit, 1);
    }

    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws RuntimeException
     */
    public function off(Bank $bank, $bit)
    {
        if ($bank->getName() === null) {
            throw new RuntimeException('Bank name not specified');
        }
        $setBit = 'setBit' . $bit;
        if(!method_exists($bank, $setBit)) {
            throw new RuntimeException(sprintf('Method %s not exist', $setBit));
        }
        $bank->{$setBit}(0);

        return sprintf('%s%s%s', $bank->getName(), $bit, 0);
    }

    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws RuntimeException
     */
    public function toggle(Bank $bank, $bit)
    {
        $getBit = 'getBit' . $bit;
        if(!method_exists($bank, $getBit)) {
            throw new RuntimeException(sprintf("Method %s not exist", $getBit));
        }
        if ($bank->{$getBit}() === 1) {
            return $this->off($bank, $bit);
        }
        if ($bank->{$getBit}() === 0) {
            return $this->on($bank, $bit);
        }

        throw new RuntimeException('Unknown status');
    }
}
