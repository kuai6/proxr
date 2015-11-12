<?php

namespace Application\Command;

/**
 * Class ContactClosure
 * @package Application\Command
 */
class ContactClosure extends AbstractCommand
{
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
}
