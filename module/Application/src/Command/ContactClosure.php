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
        $this->sequence = [self::CONTROL_COMMAND, 175, 0, 3];
        $packFormat = str_repeat('C', count($this->sequence));
        $request = call_user_func_array('pack', array_merge([$packFormat], $this->sequence));
        $this->getAdapter()->write($request);
        $response = $this->getAdapter()->read();
        $result = [];
        for ($i = 0; $i < mb_strlen($response); $i++) {
            $buf = unpack("C", $response[$i])[1];

            $result[$i][] = ($buf & 1)  ==  1   ? 1 : 0;
            $result[$i][] = ($buf & 2)  ==  2   ? 1 : 0;
            $result[$i][] = ($buf & 4)  ==  4   ? 1 : 0;
            $result[$i][] = ($buf & 8)  ==  8   ? 1 : 0;
            $result[$i][] = ($buf & 16) ==  16  ? 1 : 0;
            $result[$i][] = ($buf & 32) ==  32  ? 1 : 0;
            $result[$i][] = ($buf & 64) ==  64  ? 1 : 0;
            $result[$i][] = ($buf & 128)==  128 ? 1 : 0;
        }

        return $result;
    }
}
