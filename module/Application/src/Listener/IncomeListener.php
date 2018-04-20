<?php

namespace Application\Listener;

use Application\Service\DeviceService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Server\Event\OutcomeEvent;
use Server\Service\ServerService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class IncomeListener
 * @package Application\Listener
 */
class IncomeListener extends AbstractListenerAggregate
{

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var DeviceService
     */
    private $deviceService;

    /**
     * IncomeListener constructor.
     * @param EventManagerInterface $eventManager
     * @param DeviceService $deviceService
     */
    public function __construct(EventManagerInterface $eventManager, DeviceService $deviceService)
    {
        $this->eventManager = $eventManager;
        $this->deviceService = $deviceService;
    }


    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('income.event.'.ServerService::COMMAND_HELLO, [$this, 'onHello']);
        $this->listeners[] = $events->attach('income.event.'.ServerService::COMMAND_PING, [$this, 'onPing']);
        $this->listeners[] = $events->attach('income.event.'.ServerService::COMMAND_DATA, [$this, 'onData']);
    }


    public function onHello(EventInterface $event)
    {
        $data = $event->getParam('payload');
        $serial = $event->getParam('serial');
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');

        $type = substr($data, 0,4);

        try {
            $this->deviceService->registerDevice($type, $serial, $ip, $port);

            $outcome = new OutcomeEvent();
            $outcome->setName('outcome.event.'. ServerService::COMMAND_CONF);
            $outcome->setParams([
                'command' => ServerService::COMMAND_CONF,
                'ip'    => $ip,
                'port'  => $port,
                'data'  => sprintf('%s%s', '', ''),
            ]);
            $this->eventManager->trigger($outcome);

        } catch (\Exception $e) {
        }

    }

    public function onPing(EventInterface $event)
    {

    }

    public function onData(EventInterface $event)
    {

    }
}