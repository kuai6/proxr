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
        return sprintf("%s%s%s", $bank->getName(), $bit, 1);
    }

    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function off(Bank $bank, $bit)
    {
        if ($bank->getName() === null) {
            throw new RuntimeException('Bank name not specified');
        }
        return sprintf("%s%s%s", $bank->getName(), $bit, 0);
    }

    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws \RuntimeException
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function toggle(Bank $bank, $bit)
    {

        $getBit = 'getBit' . $bit;
        $setBit = 'setBit' . $bit;

        if(!method_exists($getBit, $bank)) {
            throw new \RuntimeException(sprintf("Method %s not exist", $getBit));
        }

        if(!method_exists($setBit, $bank)) {
            throw new \RuntimeException(sprintf("Method %s not exist", $setBit));
        }

        if ($bank->{$getBit}() == 1) {
            return $this->off($bank, $bit);
        }
        if ($bank->{$getBit}() == 0) {
            return $this->on($bank, $bit);
        }

        throw new RuntimeException('Unknown status');
    }
}
