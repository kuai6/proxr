<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;

/**
 * Class Delay
 * @package Application\Activity\Activity
 */
class Delay extends AbstractActivity
{

    protected $delayMs;

    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        if ($this->getDelayMs() !== null && (int) $this->getDelayMs() > 0) {
            usleep((int) $this->getDelayMs());
        }
        return true;
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $this->setDelayMs((string)$attributes['delayMs']);
    }

    /**
     * @return mixed
     */
    public function getDelayMs()
    {
        return $this->delayMs;
    }

    /**
     * @param mixed $delayMs
     */
    public function setDelayMs($delayMs)
    {
        $this->delayMs = $delayMs;
    }
}