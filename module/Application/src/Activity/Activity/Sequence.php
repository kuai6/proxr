<?php

namespace Application\Activity\Activity;

use Application\Activity\ActivityManager;
use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Activity\Exception;

/**
 * Последовательность активностей
 *
 * Выполняет последовательно перечисленные действия
 * Если указаны атрибуты inArray и iterator будет работать как цикл
 * и на каждой итерации подменять переменную iterator в контексте
 *
 * Class Sequence
 * @package Application\Activity\Activity\Activity
 */
class Sequence extends AbstractActivity implements ServiceLocatorAwareInterface
{
    /** @var  string */
    protected $iteratorVariable;

    /** @var  string */
    protected $arrayVariable;

    /** @var  array */
    protected $activities;

    /** @var  ActivityManager */
    protected $serviceLocator;

    /**
     * @param Context $context
     * @return mixed|void
     */
    public function execute(Context $context)
    {
        if ($this->getArrayVariable()) {
            $array = $context->get($this->getArrayVariable());
            foreach ($array as $obj) {
                $context->set($this->getIteratorVariable(), $obj);
                $this->runActivities($context);
            }
        } else {
            $this->runActivities($context);
        }
    }

    /**
     * @param Context $context
     */
    protected function runActivities(Context $context)
    {
        if ($this->getActivities()) {
            /** @var $activity AbstractActivity */
            foreach ($this->getActivities() as $activity) {
                $activity->execute($context);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed|void
     * @throws Exception\RuntimeException
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        if (isset($attributes['iterator']) && isset($attributes['inArray'])) {
            $this->setIteratorVariable((string)$attributes['iterator']);
            $this->setArrayVariable((string)$attributes['inArray']);
        }
        /** @var \SimpleXMLElement $child */
        foreach ($metadata->children() as $child) {
            $nodeName = $child->getName();
            $activity = $this->serviceLocator->get($nodeName);
            $activity->fromMetadata($child);
            if (!$activity instanceof AbstractActivity) {
                throw new Exception\RuntimeException('Wrong activity instance');
            }
            $this->addActivity($activity);
        }
    }

    /**
     * @return string
     */
    public function getArrayVariable()
    {
        return $this->arrayVariable;
    }

    /**
     * @param $arrayVariable
     * @return $this
     */
    public function setArrayVariable($arrayVariable)
    {
        $this->arrayVariable = $arrayVariable;
        return $this;
    }

    /**
     * @return string
     */
    public function getIteratorVariable()
    {
        return $this->iteratorVariable;
    }

    /**
     * @param $iteratorVariableName
     * @return $this
     */
    public function setIteratorVariable($iteratorVariableName)
    {
        $this->iteratorVariable = $iteratorVariableName;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param AbstractActivity[] $activities
     * @return $this
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
        return $this;
    }

    /**
     * @return AbstractActivity[]
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param AbstractActivity $activity
     * @return $this
     */
    public function addActivity(AbstractActivity $activity)
    {
        $this->activities[] = $activity;
        return $this;
    }
}
