<?php

namespace Application\Activity;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

/**
 * Class ActivityManagerFactory
 * @package Application\Activity
 */
class ActivityManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = ActivityManager::class;
}
