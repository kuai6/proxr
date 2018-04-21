<?php

namespace Application\Hydrator\Rest;

use Application\Entity\Bank;
use Application\Entity\Device;
use Zend\Hydrator\ExtractionInterface;

/**
 * Class DeviceHydrator
 * @package Application\Hydrator\Rest
 */
class DeviceHydrator implements ExtractionInterface
{

    private $typeManp = [
        Bank\Dac::class => 'analog relay',
        Bank\Adc::class => 'analog sensor',
        Bank\ContactClosure::class => 'digital sensor',
        Bank\Relay::class => 'digital relay',
    ];

    /**
     * Extract values from an object
     *
     * @param  Device $object
     * @return array
     */
    public function extract($object)
    {
        /** @var Bank $bank */
        $bank = $object->getBanks()->first();

        return [
            'id' => $object->getId(),
            'numberOfPins' => 8,
            'serialNumber' => $object->getName(),
            'label' => '',
            'type' => $this->typeManp[get_class($bank)],
        ];
    }
}