<?php

namespace Server\Options;

use Server\Module;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ModuleOptionsFactory
 * @package Server\Options
 */
class ModuleOptionsFactory implements FactoryInterface
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

        $config = $serviceLocator->get('config');
        return new ModuleOptions(
            array_key_exists(Module::MODULE_CONFIG_KEY, $config) ? $config[Module::MODULE_CONFIG_KEY] : []
        );
    }
}