<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;

class PeripheryServiceFactory implements FactoryInterface
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

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get('ApplicationEntityManager');


        return new PeripheryService($entityManager);
    }
}
