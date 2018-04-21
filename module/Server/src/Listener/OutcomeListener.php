<?php

namespace Server\Listener;

use Server\Service\ServerService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class OutcomeListener
 * @package Server\Listener
 */
class OutcomeListener extends AbstractListenerAggregate
{

    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * OutcomeListener constructor.
     * @param ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
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
        $this->listeners[] = $events->attach('outcome.event.'.ServerService::COMMAND_CONF, [$this, 'onOutcomeConf']);
        $this->listeners[] = $events->attach('outcome.event.'.ServerService::COMMAND_PONG, [$this, 'onOutcomePong']);
        $this->listeners[] = $events->attach('outcome.event.'.ServerService::COMMAND_DATA, [$this, 'onOutcomeData']);
    }

    /**
     * @param EventInterface $event
     */
    public function onOutcomeConf(EventInterface $event)
    {
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');
        $data = $event->getParam('data');
        $command = $event->getParam('command');
        $data = ServerService::COMMAND_CONF.$data;

        $this->serverService->send($ip, $port ,$command, $data);
    }

    public function onOutcomePong(EventInterface $event)
    {
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');

        $this->serverService->send($ip, $port, ServerService::COMMAND_PONG, ServerService::COMMAND_PONG);
    }

    public function onOutcomeData(EventInterface $event)
    {
        $ip = $event->getParam('ip');
        $port = $event->getParam('port');
        $data = $event->getParam('data');
        $this->serverService->send($ip, $port, ServerService::COMMAND_DATA, ServerService::COMMAND_DATA.$data);
    }
}