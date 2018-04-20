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
    const BIT_0_ON = '108';
    const BIT_1_ON = '109';
    const BIT_2_ON = '110';
    const BIT_3_ON = '111';
    const BIT_4_ON = '112';
    const BIT_5_ON = '113';
    const BIT_6_ON = '114';
    const BIT_7_ON = '115';

    const BIT_0_OFF = '100';
    const BIT_1_OFF = '101';
    const BIT_2_OFF = '102';
    const BIT_3_OFF = '103';
    const BIT_4_OFF = '104';
    const BIT_5_OFF = '105';
    const BIT_6_OFF = '106';
    const BIT_7_OFF = '107';

    const BIT_0_STATUS = '116';
    const BIT_1_STATUS = '117';
    const BIT_2_STATUS = '118';
    const BIT_3_STATUS = '119';
    const BIT_4_STATUS = '120';
    const BIT_5_STATUS = '121';
    const BIT_6_STATUS = '122';
    const BIT_7_STATUS = '123';

    const BIT_RESPONSE = 85;

    const STATUS_ON     = 1;
    const STATUS_OFF    = 0;


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
        $bit = sprintf('BIT_%d_ON', (int) $bit);

        $self = new \ReflectionClass(__CLASS__);
        if (!array_key_exists($bit, $self->getConstants())) {
            throw new RuntimeException(sprintf('Bit constant %s not found in %s', $bit, __CLASS__));
        }
        $this->sequence = [
            self::CONTROL_COMMAND,
            constant(sprintf('self::%s', $bit)),
            $bank->getName()
        ];
        $packFormat = 'C*';
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $response = unpack("C", $response)[1];
        if (self::BIT_RESPONSE !== $response) {
            throw new RuntimeException(sprintf('Responce is %s , but expect %s', $response, self::BIT_RESPONSE));
        }
        return true;
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
        $bit_alias = sprintf('BIT_%d_OFF', (int)$bit);

        $self = new \ReflectionClass(__CLASS__);
        if (!array_key_exists($bit_alias, $self->getConstants())) {
            throw new RuntimeException(sprintf('Bit constant %s not found in %s', $bit_alias, __CLASS__));
        }
        $this->sequence = [
            self::CONTROL_COMMAND,
            constant(sprintf('self::%s', $bit_alias)),
            $bank->getName()
        ];
        $packFormat = 'C*';
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $response = unpack("C", $response)[1];
        if (self::BIT_RESPONSE !== $response) {
            throw new RuntimeException(sprintf('Responce is %s , but expect %s', $response, self::BIT_RESPONSE));
        }
        return true;
    }

    /**
     * @param Bank $bank
     * @param int $bit
     * @return bool
     * @throws RuntimeException
     * @throws \ReflectionException
     */
    public function toggle(Bank $bank, $bit)
    {
        switch ($this->status($bank, $bit)) {
            case self::STATUS_ON:
                return $this->off($bank, $bit);
                break;
            case self::STATUS_OFF:
                return $this->on($bank, $bit);
                break;
        }
        throw new RuntimeException('Unknown status');
    }

    /**
     * @param Bank $bank
     * @param int|null $bit
     * @return int 0|1
     * @throws RuntimeException
     */
    public function status(Bank $bank, $bit = null)
    {
        if ($bank->getName() === null) {
            throw new RuntimeException('Bank name not specified');
        }

        $bit_alias = sprintf('BIT_%d_STATUS', (int)$bit);

        $self = new \ReflectionClass(__CLASS__);
        if (!array_key_exists($bit_alias, $self->getConstants())) {
            throw new RuntimeException(sprintf('Bit constant %s not found in %s', $bit_alias, __CLASS__));
        }
        $this->sequence = [
            self::CONTROL_COMMAND,
            constant(sprintf('self::%s', $bit_alias)),
            $bank->getName()
        ];
        $packFormat = 'C*';
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $response = unpack("C", $response)[1];
        return (int) $response;
    }
}
