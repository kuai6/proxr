<?php

namespace Application;

use Zend\Console\Adapter\AdapterInterface;
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
        ];
    }
}
