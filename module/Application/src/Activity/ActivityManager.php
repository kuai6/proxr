<?php

namespace Application\Activity;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class ActivityManager
 * @package Application\Activity
 */
class ActivityManager extends AbstractPluginManager
{
    /**
     * Don't share form elements by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @var array
     */
    protected $invokableClasses = [
        'activity'                  => Activity\Activity::class,
        'assign'                    => Activity\Assign::class,
        'assignfromrepository'      => Activity\Assign\FromRepository::class,
        'callback'                  => Activity\Callback::class,
        'condition'                 => Activity\Condition::class,
        'device'                    => Activity\Device::class,
        'if'                        => Activity\Condition\IfCondition::class,
        'log'                       => Activity\Log::class,
        'sequence'                  => Activity\Sequence::class,
    ];

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @throws \Exception
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ActivityInterface) {
            return ;
        }
        throw new \Exception('Wrong plugin');
    }
}
