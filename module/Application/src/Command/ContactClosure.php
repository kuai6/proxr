<?php

namespace Application\Command;

use Application\Command\Exception\RuntimeException;
use Application\Entity\Bank;

/**
 * Class ContactClosure
 * @package Application\Command
 */
class ContactClosure extends AbstractCommand
{
    const STATUS_CODE = 175;

    const BIT_0 = 0;
    const BIT_1 = 1;
    const BIT_2 = 2;
    const BIT_3 = 3;
    const BIT_4 = 4;
    const BIT_5 = 5;
    const BIT_6 = 6;
    const BIT_7 = 7;




    /**
     *
     */
    public function getAllStatuses()
    {
        $this->sequence = [self::CONTROL_COMMAND, 175, 0, 7];
        $packFormat = 'C*';
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $result = [];
        for ($i = 0; $i < mb_strlen($response); $i++) {
            $buf = unpack("C", $response[$i])[1];
            $bankName = $i+1;
            $result[$bankName][] = ($buf & 1)  ==  1   ? 1 : 0;
            $result[$bankName][] = ($buf & 2)  ==  2   ? 1 : 0;
            $result[$bankName][] = ($buf & 4)  ==  4   ? 1 : 0;
            $result[$bankName][] = ($buf & 8)  ==  8   ? 1 : 0;
            $result[$bankName][] = ($buf & 16) ==  16  ? 1 : 0;
            $result[$bankName][] = ($buf & 32) ==  32  ? 1 : 0;
            $result[$bankName][] = ($buf & 64) ==  64  ? 1 : 0;
            $result[$bankName][] = ($buf & 128)==  128 ? 1 : 0;
        }

        return $result;
    }


    public function getStatus(Bank $bank, $bit = null)
    {
        if ($bank->getName() === null) {
            throw new RuntimeException('Bank name not specified');
        }

        $this->sequence = [
            self::CONTROL_COMMAND,
            self::STATUS_CODE,
            $bank->getName()];
        $packFormat = 'C*';
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $response = unpack("C", $response)[1];

        $byte = [
            self::BIT_0 => ($response & 1)  ==  1   ? 1 : 0,
            self::BIT_1 => ($response & 2)  ==  2   ? 1 : 0,
            self::BIT_2 => ($response & 4)  ==  4   ? 1 : 0,
            self::BIT_3 => ($response & 8)  ==  8   ? 1 : 0,
            self::BIT_4 => ($response & 16) ==  16  ? 1 : 0,
            self::BIT_5 => ($response & 32) ==  32  ? 1 : 0,
            self::BIT_6 => ($response & 64) ==  64  ? 1 : 0,
            self::BIT_7 => ($response & 128)==  128 ? 1 : 0,
        ];

        return ($bit !== null) ? $byte[$bit] : $byte;
    }
}
