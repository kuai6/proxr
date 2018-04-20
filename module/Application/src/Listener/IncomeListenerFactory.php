<?php

namespace Application\Listener;

use Application\Service\DeviceService;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class IncomeListenerFactory
 * @package Application\Listener
 */
class IncomeListenerFactory implements FactoryInterface
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

        /** @var DeviceService $deviceService */
        $deviceService = $serviceLocator->get(DeviceService::class);
        /** @var Application $application */
        $application = $serviceLocator->get('Application');

        return new IncomeListener($application->getEventManager(), $deviceService);
    }
}