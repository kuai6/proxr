<?php

namespace Application\Controller;

use Application\Service\Queue;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConsoleControllerFactory
 * @package Application\Controller
 */
class ConsoleControllerFactory implements FactoryInterface
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

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $serviceLocator->get('doctrine.entity_manager.orm_default');

        /** @var array $config */
        $config = $serviceLocator->get('config');

        /** @var Queue $queueService */
        $queueService = $serviceLocator->get(Queue::class);

        return new ConsoleController($config, $entityManager, $queueService);
    }
}