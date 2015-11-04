<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * Class Device
 * @package Application\Entity
 */
class Device extends AbstractDevice
{
    /**
     * Device name
     * @var string
     *
     * @ORM\Column(name="name", nullable=false)
     */
    protected $name;

    /**
     * DeviceStatus
     * @var DeviceStatus
     *
     * @ORM\Column(name="statusId")
     * @ORM\ManyToOne(targetEntity="\Application\Entity\DeviceStatus", fetch="LAZY")
     */
    protected $status;
}
