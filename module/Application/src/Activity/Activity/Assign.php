<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Set context variable
 *
 * Class Assign
 * @package Application\Activity\Activity
 */
class Assign extends AbstractActivity implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var  string */
    protected $value;

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        $value = $this->getValue();
        $context->set($this->getName(), $value);
        return $value;
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $this->setName((string)$attributes['name']);

        if (isset($attributes['value'])) {
            $this->setValue((string)$attributes['value']);
        }
    }
}
