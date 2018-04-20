<?php

namespace Application\Activity;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InvokerFactory
 * @package Application\Activity
 */
class InvokerFactory implements FactoryInterface
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

        /** @var ActivityManager $activityManager */
        $activityManager = $serviceLocator->get(ActivityManager::class);

        return new Invoker($activityManager, $serviceLocator);
    }
}