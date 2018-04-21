<?php

namespace Application\Controller;

use Application\Service\DeviceService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class indexControllerFactory
 * @package Application\Controller
 */
class indexControllerFactory implements FactoryInterface
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

        return new IndexController($deviceService);
    }
}