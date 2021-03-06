<?php

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DeviceServiceFactory
 * @package Application\Service
 */
class DeviceServiceFactory implements FactoryInterface
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
        /** @var BankService $bankService */
        $bankService = $serviceLocator->get(BankService::class);
        /** @var Application $application */
        $application = $serviceLocator->get('Application');
        /** @var Logger $logger */
        $logger = $serviceLocator->get('logger');

        return new DeviceService(
            $entityManager,
            $bankService,
            $application->getEventManager(),
            $logger);
    }
}