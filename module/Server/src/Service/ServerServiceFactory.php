<?php

namespace Server\Service;
use Server\Options\ModuleOptions;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServerServiceFactory
 * @package Server\Service
 */
class ServerServiceFactory implements FactoryInterface
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

        /** @var Application $application */
        $application = $serviceLocator->get('Application');
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);
        /** @var LoggerInterface $logger */
        $logger = $serviceLocator->get('ServerLogger');
        return new ServerService($moduleOptions, $application->getEventManager(), $logger);
    }
}