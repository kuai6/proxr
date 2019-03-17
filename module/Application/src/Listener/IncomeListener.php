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
use Zend\Log\Logger;

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
     * @var Logger
     */
    private $logger;

    /**
     * IncomeListener constructor.
     * @param EventManagerInterface $eventManager
     * @param DeviceService $deviceService
     * @param Logger $logger
     */
    public function __construct(
        EventManagerInterface $eventManager,
        DeviceService $deviceService,
        Logger $logger)
    {
        $this->eventManager = $eventManager;
        $this->deviceService = $deviceService;
        $this->logger = $logger;
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

        $banksCount = ord(substr($data, 0,1));

        $banks = [];
        if ($banksCount > 0) {
            $pos = 1;
            do {
                $bData = substr($data, $pos, 6);
                if (strlen($bData) < 6) {
                    break;
                }
                $b = [
                    'type' => substr($bData, 0, 4),
                    'id' => ord(substr($bData, 4, 1)),
                    'p_cnt' => ord(substr($bData, 5, 1)),
                ];
                $this->logger->debug(
                    sprintf("Received device bank info: id=%s, p_cnt=%d, type=%s",
                        $b['id'], $b['p_cnt'], $b['type']));
                $banks[] = $b;
                $pos += 6;
                $banksCount--;
            } while ($banksCount > 0);
        }

        try {
            $this->deviceService->registerDevice($serial, $ip, $port, $banks);

            $outcome = new OutcomeEvent();
            $outcome->setName('outcome.event.'. ServerService::COMMAND_CONF);
            $outcome->setParams([
                'command' => ServerService::COMMAND_CONF,
                'ip'    => $ip,
                'port'  => $port,
                'data'  => "\x88\x13\x00\x00\xf4\x01\x00\x00",
            ]);
            $this->eventManager->trigger($outcome);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function onPing(EventInterface $event)
    {
        $serial = $event->getParam('serial');
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');


        try {
            $this->deviceService->ping($serial, $ip, $port);

            $outcome = new OutcomeEvent();
            $outcome->setName('outcome.event.'. ServerService::COMMAND_PONG);
            $outcome->setParams([
                'command' => ServerService::COMMAND_PONG,
                'ip'    => $ip,
                'port'  => $port,
            ]);
            $this->eventManager->trigger($outcome);

        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function onData(EventInterface $event)
    {
        $serial = $event->getParam('serial');
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');
        $data = $event->getParam('payload');

        try {
            $this->deviceService->ping($serial, $ip, $port);
            $this->deviceService->handle($serial, $ip, $port, $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}