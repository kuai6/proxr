<?php

namespace Application\Hydrator\Rest;

use Application\Entity\Periphery\PeripheryType;
use Zend\Hydrator\HydrationInterface;

class PeripheryTypeMapper implements HydrationInterface
{

    /**
     * Extract values from an object
     *
     * @param  PeripheryType $object
     * @return array
     */
    public function extract($object)
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'icon' => $object->getIcon(),
            'inputs' => $object->getInputs(),
            'outputs' => $object->getOutputs()
        ];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  PeripheryType $object
     * @return PeripheryType
     */
    public function hydrate(array $data, $object)
    {
        $object->setName($data['name']);
        if ($data.contains('description')) $object->setDescription($data('description'));
        if ($data.contains('icon')) $object->setDescription($data('icon'));
        $object->setInputs($data['inputs']);
        $object->setOutputs($data['outputs']);

        return $object;
    }
}
