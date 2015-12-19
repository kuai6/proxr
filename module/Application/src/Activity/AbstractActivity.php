<?php

namespace Application\Activity;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Activity
 * @package Application\Activity
 */
abstract class AbstractActivity implements ActivityInterface
{
    /** @var  string */
    protected $name;

    /**
     * @param Context $context
     * @return mixed
     */
    abstract public function execute(Context $context);

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    abstract public function fromMetadata($metadata);

    /**
     * @param \SimpleXMLElement $xml
     * @return AbstractActivity
     * @throws Exception\Exception
     */
    protected function createActivityFromXml(\SimpleXMLElement $xml)
    {
        $activityManager = null;
        if ($this instanceof ServiceLocatorAwareInterface && $this->getServiceLocator() instanceof ActivityManager) {
            $activityManager = $this->getServiceLocator();
        }
        if (method_exists($this, 'getContext')) {
            $activityManager = $this->getContext()->getActivityManager();
        }

        if (!$activityManager) {
            throw new Exception\Exception('activities manager not found');
        }

        $nodeName = $xml->getName();
        /** @var AbstractActivity $activity */
        $activity = $activityManager->get($nodeName);
        $activity->fromMetadata($xml);

        return $activity;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return AbstractActivity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
