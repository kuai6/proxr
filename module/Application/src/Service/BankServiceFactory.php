<?php

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class BankServiceFactory
 * @package Application\Service
 */
class BankServiceFactory implements FactoryInterface
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

        return new BankService($entityManager);
    }
}