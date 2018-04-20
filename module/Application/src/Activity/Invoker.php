<?php

namespace Application\Activity;

use Application\Activity\Activity\Activity;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Invoker
 * @package Application\Activity
 */
class Invoker
{
    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * @var ServiceManager
     */
    private $serviceLocator;

    /**
     * Invoker constructor.
     * @param ActivityManager $activityManager
     * @param ServiceManager $serviceLocator
     */
    public function __construct(ActivityManager $activityManager, ServiceManager $serviceLocator)
    {
        $this->activityManager = $activityManager;
        $this->serviceLocator = $serviceLocator;
    }


    /**
     * @param Activity $activity
     * @param array $arguments
     * @param string $metadata
     * @return Context
     */
    public function invoke(Activity $activity, $arguments = [], $metadata)
    {
        $context = $this->getActivityContext($activity, $metadata);
        foreach ($arguments as $k => $v) {
            $context->$k = $v;
        }
        $context->setArguments($arguments);
        $context->set('serviceLocator', $this->serviceLocator);

        $context->setActivityManager($this->activityManager);

        $context->getActivity()->execute($context);

        return $context;
    }

    /**
     * @param Activity $activity
     * @param string $metadata
     * @return Context
     */
    protected function getActivityContext(Activity $activity, $metadata)
    {
        $context = new Context();
        $metadata = new \SimpleXMLElement($metadata);
        if ($metadata) {
            $context->setMetadata($metadata);
        }
        $context->set('activity', $activity);
        return $context;
    }
}
