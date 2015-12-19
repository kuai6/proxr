<?php

namespace Application\Activity;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Context
 * @package Application\Activity
 */
class Context
{
    /** @var  string */
    protected $id;

    /** @var array */
    protected $context;

    /** @var  array */
    protected $arguments;

    /** @var  ActivityManager */
    protected $activityManager;

    /** @var  \SimpleXmlElement */
    protected $metadata;

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->context[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->context) ? $this->context[$name] : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->__set($name, $value);
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->context);
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->__get('serviceLocator');
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param $argName
     *
     * @return mixed
     */
    public function getArgument($argName)
    {
        if (array_key_exists($argName, $this->arguments)) {
            return $this->arguments[$argName];
        }

        throw new \InvalidArgumentException(sprintf('Argument %s in context %s not found!', $argName, get_class($this)));
    }

    /**
     * @param $argName
     * @return bool
     */
    public function hasArgument($argName)
    {
        return array_key_exists($argName, $this->arguments);
    }

    /**
     * @return ActivityManager
     */
    public function getActivityManager()
    {
        return $this->activityManager;
    }

    /**
     * @param ActivityManager $activityManager
     */
    public function setActivityManager($activityManager)
    {
        $this->activityManager = $activityManager;
    }

    /**
     * @return \SimpleXmlElement
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param \SimpleXmlElement $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return AbstractActivity
     */
    public function getActivity()
    {
        return $this->__get('activity');
    }
}
