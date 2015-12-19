<?php

namespace Application\Event;

/**
 * Class Event
 * @package Application\Event
 */
class Event extends AbstractEvent
{
    const EVENT_CONTACT_CLOSURE = 'event.contactClosure';

    /** @var  string */
    protected $name;

    /** @var  int */
    protected $device;

    /** @var  int */
    protected $bank;

    /** @var  int */
    protected $bits;

    /** @var  string */
    protected $on;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param int $device
     * @return Event
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return int
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param int $bank
     * @return Event
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
        return $this;
    }

    /**
     * @return int
     */
    public function getBits()
    {
        return $this->bits;
    }

    /**
     * @param int $bits
     * @return Event
     */
    public function setBits($bits)
    {
        $this->bits = $bits;
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
     * @return Event
     */
    public function setOn($on)
    {
        $this->on = $on;
        return $this;
    }
}
