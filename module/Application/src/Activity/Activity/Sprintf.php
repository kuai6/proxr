<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Sprintf
 * @package Application\Activity\Activity
 */
class Sprintf extends AbstractActivity implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $out;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        $ar = [];
        foreach ($this->args as $k => $v) {
            $ar[$k] = $v;
            if ($context->has($v)) {
                $ar[$k] = $context->get($v);
            }
        }

        $result = vsprintf($this->getFormat(), $ar);

        if ($this->getOut()) {
            $context->set($this->getOut(), $result);
        }
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $format = (string) $attributes['format'];
        if ($format === '') {
            throw new \RuntimeException('Attribute format must be specified');
        }
        $this->setFormat($format);

        if ($attributes['out']) {
            $this->setOut((string) $attributes['out']);
        }

        /** @var \SimpleXMLElement $child */
        foreach ($metadata->children() as $child) {
//            $nodeName = $child->getName();
            $attr = $child->attributes();
            $this->args[] = (string) $attr['name'];
        }
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * @param string $out
     * @return $this
     */
    public function setOut($out)
    {
        $this->out = $out;
        return $this;
    }


}