<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Application\Activity\Exception\RuntimeException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Выполняет проверку условия и возвращает результат
 *
 * Class Condition
 * @package Application\Activity\Activity
 */
class Condition extends Callback implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var  string */
    protected $type;

    /** @var  AbstractActivity */
    protected $activity;

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param AbstractActivity $activity
     * @return $this
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @return AbstractActivity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param Context $context
     * @return bool
     * @throws RuntimeException
     */
    public function execute(Context $context)
    {
        if ($this->getType()) {
            $activity = $this->getServiceLocator()->get($this->getType());
            if (!$activity instanceof Condition) {
                throw new RuntimeException($this->getType() . ' activity is not condition');
            }
            return (bool)$activity->execute($context);
        } else {
            if($this->getActivity()) {
                $res = $this->getActivity()->execute($context);
            } else {
                $res = parent::execute($context);
            }
            return (bool)$res;
        }
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return void
     */
    public function fromMetadata($metadata)
    {
        parent::fromMetadata($metadata);
        if ($metadata['type']) {
            $this->setType((string)$metadata['type']);
        }
        if ($metadata->children()->count()) {
            /** @var \SimpleXMLElement $children */
            $children = $metadata->children()[0];
            /** @var AbstractActivity $activity */
            $activity = $this->getServiceLocator()->get($children->getName());
            $activity->fromMetadata($children);
            $this->setActivity($activity);
        }
    }
}