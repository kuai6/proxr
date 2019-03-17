<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\EntityRepository\Activity")
 *
 * Class Activity
 * @package Application\Entity
 */
class Activity extends AbstractActivity
{
    const ACTIVITY_BIT_RAISE    = 'raise';
    const ACTIVITY_BIT_FALL     = 'fall';

    /**
     * Name
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * Description
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * Status
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    protected $status;

    /**
     * Activity Xml metadata
     * @var string
     *
     * @ORM\Column(name="metadata", type="text", nullable=false)
     */
    protected $metadata;

    /**
     * Event name
     * @var string
     *
     * @ORM\Column(name="event", type="string", nullable=false)
     */
    protected $event;

    /**
     * Device
     * @var Device
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Device", fetch="LAZY")
     * @ORM\JoinColumn(name="deviceId", referencedColumnName="id", nullable=false)
     */
    protected $device;

    /**
     * Bind bank
     * @var Bank
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Bank", fetch="LAZY")
     * @ORM\JoinColumn(name="bankId", referencedColumnName="id", nullable=false)
     */
    protected $bank;

    /**
     * Bit name
     * @var int
     *
     * @ORM\Column(name="bit", type="string", nullable=false)
     */
    protected $bit;

    /**
     * Change direction, i.e raise, fall or null
     * @var string
     *
     * @ORM\Column(name="on_event", type="string", nullable=true)
     */
    protected $on;

    /**
     * Actions graph nodes raw json
     * @var string
     *
     * @ORM\Column(name="nodes", type="string")
     */
    protected $nodes;

    /**
     * Actions graph links raw json
     * @var string
     *
     * @ORM\Column(type="string", type="string")
     */
    protected $links;

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Activity
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getNodes(): ?string
    {
        return $this->nodes;
    }

    /**
     * @param string $nodes
     */
    public function setNodes(string $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @return string
     */
    public function getLinks(): ?string
    {
        return $this->links;
    }

    /**
     * @param string $links
     */
    public function setLinks(string $links): void
    {
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param string $metadata
     * @return Activity
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
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
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param Device $device
     * @return Activity
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     * @return Activity
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
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
