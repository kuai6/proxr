<?php

namespace Application\Service;
use Application\Activity\ActivityManager;
use Application\Activity\Invoker;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ActivityFactory
 * @package Application\Service
 */
class ActivityFactory implements FactoryInterface
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
        /** @var Invoker $activityInvoker */
        $activityInvoker = $serviceLocator->get(Invoker::class);
        /** @var ActivityManager $activityManager */
        $activityManager = $serviceLocator->get(ActivityManager::class);

        return new ActivityListener($entityManager, $activityManager, $activityInvoker);
    }
}