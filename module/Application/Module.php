<?php

namespace Application;

use Application\Activity\ActivitiesProviderInterface;
use Application\Activity\ActivityManager;
use Application\Event\Event;
use Application\Listener\IncomeListener;
use Application\Service\Activity;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package Application
 */
class Module implements ConfigProviderInterface
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
        /** @var IncomeListener $incomeListener */
        $incomeListener = $serviceLocator->get(IncomeListener::class);
        $incomeListener->attach($eventManager);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @param AdapterInterface $console
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'Shelled Controller',
            'system init' => 'Init system'
        ];
    }
}
