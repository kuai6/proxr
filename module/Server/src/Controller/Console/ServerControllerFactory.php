<?php

namespace Server\Controller\Console;

use Server\Options\ModuleOptions;
use Server\Service\ServerService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServerControllerFactory
 * @package Server\Controller\Console
 */
class ServerControllerFactory implements FactoryInterface
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
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);
        /** @var ServerService $serverService */
        $serverService = $serviceLocator->get(ServerService::class);

        return new ServerController($moduleOptions, $serverService);
    }
}