<?php

namespace Application\Controller;

use Application\Hydrator\Rest\ActivityMapper;
use Application\Service\ActivityService;
use Application\Service\PeripheryService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ActivityControllerFactory implements FactoryInterface
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

        /** @var ActivityService $activityService */
        $activityService = $serviceLocator->get(ActivityService::class);

        /** @var PeripheryService $peripheryService */
        $peripheryService = $serviceLocator->get(PeripheryService::class);

        return new ActivityController($activityService, new ActivityMapper($peripheryService));
    }
}
