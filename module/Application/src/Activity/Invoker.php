<?php

namespace Application\Activity;

use Application\Activity\Activity\Activity;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Invoker
 * @package Application\Activity
 */
class Invoker implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param Activity $activity
     * @param array $arguments
     * @param string $metadata
     * @return Context
     */
    public function invoke(Activity $activity, $arguments = [], $metadata)
    {
        $sl = $this->getServiceLocator();
        $context = $this->getActivityContext($activity, $metadata);
        foreach ($arguments as $k => $v) {
            $context->$k = $v;
        }
        $context->setArguments($arguments);
        $context->set('serviceLocator', $sl);
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getServiceLocator()->get(ActivityManager::class);
        $context->setActivityManager($activityManager);

        $context->getActivity()->execute($context);

        return $context;
    }

    /**
     * @param Activity $activity
     * @param string $metadata
     * @return Context
     */
    protected function getActivityContext(Activity &$activity, $metadata)
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
