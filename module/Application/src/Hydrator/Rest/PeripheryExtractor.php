<?php

namespace Application\Hydrator\Rest;

use Application\Entity\Periphery\PeripheryUnit;
use Zend\Hydrator\ExtractionInterface;

class PeripheryExtractor implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  PeripheryUnit $object
     * @return array
     */
    public function extract($object)
    {
        return [
            'id' => $object->getId(),
            'type_id' => $object->getType()->getId(),
            'device_id' => $object->getDevice()->getId(),
            'bank_id' => $object->getBank()->getId(),
            'bit' => $object->getBit()
        ];
    }
}