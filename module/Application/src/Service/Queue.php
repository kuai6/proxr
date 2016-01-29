<?php

namespace Application\Service;

use Kuai6\Queue\Exchange;
use Kuai6\Queue\Queue as AmqpQueue;
use Kuai6\Queue\Server;
use Kuai6\Queue\ServerFactory;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class QeueService
 * @package Application\Service
 */
class Queue extends AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param $config
     * @return bool
     * @throws \Exception
     */
    public function initExchanges($config)
    {
        if (count($config) === 0) {
            return true;
        }
        /** @var Server $queueServer */
        $queueServer = $this->getServiceLocator()->get(ServerFactory::class);
        try {
            foreach ($config as $exchangeName => $exchangeConfig) {
                $type = AMQP_EX_TYPE_DIRECT;
                if (array_key_exists('type', $exchangeConfig)) {
                    $type = $exchangeConfig['type'];
                }
                $exchange = new Exchange($exchangeName, $type, $exchangeConfig);
                $queueServer->deleteExchange($exchange);
                $queueServer->declareExchange($exchange);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * @param $config
     * @return bool
     * @throws \Exception
     */
    public function initQueues($config)
    {
        if (count($config) === 0) {
            return true;
        }
        /** @var Server $queueServer */
        $queueServer = $this->getServiceLocator()->get(ServerFactory::class);
        try {
            foreach ($config as $queueName => $queueConfig) {
                $options = [];
                if (array_key_exists('options', $queueConfig)) {
                    $options = $queueConfig['options'];
                }
                $queue = new AmqpQueue($queueName, $options);
                $queueServer->deleteQueue($queue);
                $queueServer->declareQueue($queue);
                if (array_key_exists('bindTo', $queueConfig)) {
                    $routingKey = '';
                    if (array_key_exists('routingKey', $queueConfig)) {
                        $routingKey = $queueConfig['routingKey'];
                    }
                    $queueServer->queueBind($queue, $queueConfig['bindTo'], $routingKey);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }
}
