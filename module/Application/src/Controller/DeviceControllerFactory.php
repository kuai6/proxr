<?php

namespace Application\Controller;

use Application\Hydrator\Rest\DeviceHydrator;
use Application\Service\DeviceService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeviceControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return DeviceController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var $deviceService DeviceService */
        $deviceService = $serviceLocator->get(DeviceService::class);

        return new DeviceController($deviceService, new DeviceHydrator());
    }
}
