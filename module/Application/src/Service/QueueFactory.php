<?php

namespace Application\Service;
use Kuai6\Queue\Server;
use Kuai6\Queue\ServerFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class QueueFactory
 * @package Application\Service
 */
class QueueFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }
        /** @var Server $queueServer */
        $queueServer = $serviceLocator->get(ServerFactory::class);

        return new Queue($queueServer);
    }
}