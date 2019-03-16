<?php

namespace Application\Controller;

use Application\Hydrator\Rest\PeripheryExtractor;
use Application\Hydrator\Rest\PeripheryTypeMapper;
use Application\Service\PeripheryService;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PeripheryControllerFactory implements FactoryInterface
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

        /** @var PeripheryService $peripheryService */
        $peripheryService = $serviceLocator->get(PeripheryService::class);

        return new PeripheryController($peripheryService, new PeripheryTypeMapper(),
            new PeripheryExtractor());
    }
}
