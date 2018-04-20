<?php

namespace Server;
use Server\Listener\OutcomeListener;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package Server
 */
class Module implements
    ConfigProviderInterface,
    ServiceProviderInterface,
    ControllerProviderInterface,
    ConsoleUsageProviderInterface
{
    const MODULE_CONFIG_KEY = 'server';




    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to seed
     * such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerConfig()
    {
        return include __DIR__ .'/config/controller.config.php';
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return array_merge_recursive(
            include __DIR__ .'/config/module.config.php',
            include __DIR__ .'/config/console.config.php'
        );
    }

    /**
     * Returns an array or a string containing usage information for this module's Console commands.
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
     * Console and send output.
     *
     * If the result is a string it will be shown directly in the console window.
     * If the result is an array, its contents will be formatted to console window width. The array must
     * have the following format:
     *
     *     return array(
     *                'Usage information line that should be shown as-is',
     *                'Another line of usage info',
     *
     *                '--parameter'        =>   'A short description of that parameter',
     *                '-another-parameter' =>   'A short description of another parameter',
     *                ...
     *            )
     *
     * @param AdapterInterface $console
     * @return array|string|null
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'UDP server',
            'server run' => 'Run controller'
        ];
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceLocator = $e->getApplication()->getServiceManager();
        /** @var OutcomeListener $incomeListener */
        $incomeListener = $serviceLocator->get(OutcomeListener::class);
        $incomeListener->attach($eventManager);
    }
}