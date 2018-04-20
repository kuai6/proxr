<?php

namespace Server\Listener;

use Server\Service\ServerService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OutcomeListenerFactory
 * @package Server\Listener
 */
class OutcomeListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var ServerService $serverService */
        $serverService = $serviceLocator->get(ServerService::class);

        return new OutcomeListener($serverService);
    }
}