<?php

namespace Application;

use Application\Activity\ActivitiesProviderInterface;
use Application\Activity\ActivityManager;
use Application\Event\Event;
use Application\Service\Activity;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package Application
 */
class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager = $e->getApplication()->getServiceManager();
        /** @var ServiceListenerInterface $serviceListener */
        $serviceListener = $serviceManager->get('ServiceListener');
        $serviceListener->addServiceManager(
            ActivityManager::class,
            'activities',
            ActivitiesProviderInterface::class,
            'getActivitiesConfig'
        );
        $serviceLocator = $e->getApplication()->getServiceManager();
        /** @var Activity $activityService */
        $activityService = $serviceLocator->get(Activity::class);
        $e->getApplication()->getEventManager()->attach(Event::EVENT_CONTACT_CLOSURE, [$activityService, 'contactClosureEventHandler']);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/',
                ],
            ],
        ];
    }

    /**
     * @param AdapterInterface $console
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'Test Daemon',
            /** import */
            'test (start|stop|restart):command [--logPath=] [--processPath=] [--childNumber=]' => 'Dummy Test Daemon',
            'system init' => 'Init system'
        ];
    }
}
