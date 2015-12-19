<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Application\Activity\ContextTrait;
use Application\Activity\Exception\RuntimeException;

/**
 * Class Activity
 * @package Application\Activity\Activity
 */
class Activity extends AbstractActivity
{
    use ContextTrait;

    /** @var  Sequence */
    protected $trigger;

    protected $initialized = false;

    /** @var  string */
    protected $event;

    /** @var  string */
    protected $bank;

    /** @var  int */
    protected $bit;

    /** @var  string */
    protected $on;

    /**
     * @param Context $context
     * @return mixed|void
     * @throws RuntimeException
     */
    public function execute(Context $context)
    {
        $this->setContext($context);
        $this->init();
        $this->getTrigger()->execute($this->getContext());
    }


    public function init()
    {
        if (!$this->initialized) {
            $this->fromMetadata($this->getContext()->getMetadata());
            $this->initialized = true;
        }
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        if ($attributes['event']) {
            $this->setEvent((string) $attributes['event']);
        }

        if ($attributes['bit']) {
            $this->setBit((string) $attributes['bit']);
        }
        if ($attributes['bank']) {
            $this->setBank((string) $attributes['bank']);
        }
        if ($attributes['on']) {
            $this->setOn((string) $attributes['on']);
        }

        if ($metadata->children()->count() > 0) {
            if ($metadata->trigger->children()->count() > 0) {
                /** @var Sequence $trigger */
                $trigger = $this->createActivityFromXml($metadata->trigger->children());
                $this->setTrigger($trigger);
            }
        }
    }

    /**
     * @return Sequence
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * @param Sequence $trigger
     * @return Activity
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return Activity
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit()
    {
        return $this->bit;
    }

    /**
     * @return string
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param string $bank
     * @return Activity
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
        return $this;
    }

    /**
     * @param int $bit
     * @return Activity
     */
    public function setBit($bit)
    {
        $this->bit = $bit;
        return $this;
    }

    /**
     * @return string
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * @param string $on
     * @return Activity
     */
    public function setOn($on)
    {
        $this->on = $on;
        return $this;
    }
}
