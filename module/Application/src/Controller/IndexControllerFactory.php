<?php

namespace Application\Controller;

use Application\Options\ModuleOptions;
use Application\Service\ActivityService;
use Application\Service\DeviceService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class indexControllerFactory
 * @package Application\Controller
 */
class IndexControllerFactory implements FactoryInterface
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
        /** @var ActivityService $activityService */
        $activityService = $serviceLocator->get(ActivityService::class);
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get(ModuleOptions::class);

        return new IndexController($moduleOptions, $deviceService, $activityService);
    }
}