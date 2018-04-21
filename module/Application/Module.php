<?php

namespace Application;

use Application\Activity\ActivitiesProviderInterface;
use Application\Activity\ActivityManager;
use Application\Event\Event;
use Application\Listener\IncomeListener;
use Application\Service\ActivityListener;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package Application
 */
class Module implements ConfigProviderInterface, InitProviderInterface
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceLocator = $e->getApplication()->getServiceManager();
        /** @var ActivityListener $activityListener */
        $activityListener = $serviceLocator->get(ActivityListener::class);
        $activityListener->attach($eventManager);
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

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        $serviceManager = $manager->getEvent()->getParam('ServiceManager');
        /** @var ServiceListenerInterface $serviceListener */
        $serviceListener = $serviceManager->get('ServiceListener');
        $serviceListener->addServiceManager(
            ActivityManager::class,
            'activities',
            ActivitiesProviderInterface::class,
            'getActivitiesConfig'
        );
    }
}
