<?php
/**
 * Created by PhpStorm.
 * User: kuai6
 * Date: 12.03.19
 * Time: 13:23
 */

namespace Application\Options;


use Application\Module;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ModuleOptionsFactory
 * @package Application\Options
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

        /** @var array $config */
        $config = $serviceLocator->get('config');
        return new ModuleOptions(
            array_key_exists(Module::MODULE_CONFIG_KEY, $config) ? $config[Module::MODULE_CONFIG_KEY]: []
        );
    }
}