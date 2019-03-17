<?php

namespace Application\Hydrator\Rest;

use Application\Entity\Activity;
use Application\Entity\Bank\Adc;
use Application\Entity\Bank\ContactClosure;
use Application\Event\Event;
use Application\Service\PeripheryService;
use Zend\Hydrator\HydratorInterface;

class ActivityMapper implements HydratorInterface
{
    /**
     * @var PeripheryService
     */
    private $peripheryService;

    /**
     * ActivityMapper constructor.
     * @param PeripheryService $peripheryService
     */
    public function __construct(PeripheryService $peripheryService)
    {
        $this->peripheryService = $peripheryService;
    }

    /**
     * Extract values from an object
     *
     * @param Activity $object
     * @return array
     */
    public function extract($object)
    {
        $peripheryUnit = $this->peripheryService->findUnit(
            $object->getDevice()->getId(), $object->getBank()->getId(), $object->getBit());

        return [
            'id' => $object->getId() ?: '',
            'name' => $object->getName(),
            'description' => $object->getDescription() ?: '',
            'periphery_id' => $peripheryUnit->getId(),
            'device_id' => $peripheryUnit->getDevice()->getId(),
            'event_type' => ($object->getOn() == Activity::ACTIVITY_BIT_RAISE) ? 'on_rise' : 'on_down',
            'nodes' => $object->getNodes(),
            'links' => $object->getLinks()
        ];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  Activity $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $peripheryUnit = $this->peripheryService->getUnit($data['periphery_id']);

        $object->setName($data['name']);
        if (array_key_exists('description', $data)) $object->setDescription($data['description']);
        $object->setDevice($peripheryUnit->getDevice());
        $object->setBank($peripheryUnit->getBank());
        $object->setBit($peripheryUnit->getBit());
        $object->setNodes($data['nodes']);
        $object->setLinks($data['links']);
        $object->setOn($data['event_type'] == 'on_rise' ? Activity::ACTIVITY_BIT_RAISE : Activity::ACTIVITY_BIT_FALL);
        $event = '';
        switch (true) {
            case $peripheryUnit->getBank() instanceof Adc:
                $event = Event::EVENT_ADC;
                break;
            case $peripheryUnit->getBank() instanceof ContactClosure:
                $event = Event::EVENT_CONTACT_CLOSURE;
                break;
        }
        $object->setEvent($event);

        return $object;
    }
}