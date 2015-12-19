<?php

namespace Application\Activity\Activity;

use Application\Activity\Context;

/**
 * Class Callback
 * @package Application\Activity\Activity
 */
class Callback extends Activity
{
    /** @var  string */
    protected $class;

    /** @var  string */
    protected $method;

    /**
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function execute(Context $context)
    {
        $class = $this->getClass();
        if (!$class) {
            $class = $context->getActivity();
        }
        return call_user_func_array([$class, $this->getMethod()], [$this, $context]);
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return void
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        if ($attributes['name']) {
            $this->setName((string)$attributes['name']);
        }
        if ($attributes['class']) {
            $this->setClass((string)$attributes['class']);
        }
        $this->setMethod((string)$attributes['method']);
    }
}
