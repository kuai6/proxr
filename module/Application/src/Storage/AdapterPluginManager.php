<?php

namespace Application\Storage;

use Application\Storage\Adapter\ConsoleOptionsAdapter;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Class AdapterPluginManager
 * @package Application\Storage
 */
class AdapterPluginManager extends AbstractPluginManager
{
    protected $invokableClasses = [
        'consoleOptions' => ConsoleOptionsAdapter::class
    ];

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
    }
}
