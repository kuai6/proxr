<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\EntityRepository\EventLog")
 *
 * Class EventLog
 * @package Application\Entity
 */
class EventLog extends AbstractEventLog
{
    /**
     * Date and time with fractal microseconds
     * @var \DateTime
     *
     * @ORM\Column(name="dateTime", type="datetime", nullable=false, columnDefinition="DATETIME(6) NOT NULL")
     */
    protected $dateTime;

    /**
     * Device
     * @var Device
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Device", fetch="LAZY")
     * @ORM\JoinColumn(name="deviceId", referencedColumnName="id", nullable=false)
     */
    protected $device;

    /**
     * Bank
     * @var Bank
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Bank", fetch="LAZY")
     * @ORM\JoinColumn(name="bankId", referencedColumnName="id", nullable=false)
     */
    protected $bank;

    /**
     * Bank bit 0
     * @var integer
     *
     * @ORM\Column(name="bit0", type="integer", nullable=true)
     */
    protected $bit0;

    /**
     * Change direction of bit 0
     * @var string
     *
     * @ORM\Column(name="bit0_direction", type="string", nullable=true)
     */
    protected $bit0_direction;

    /**
     * Bank bit 1
     * @var integer
     *
     * @ORM\Column(name="bit1", type="integer", nullable=true)
     */
    protected $bit1;

    /**
     * Change direction of bit 1
     * @var string
     *
     * @ORM\Column(name="bit1_direction", type="string", nullable=true)
     */
    protected $bit1_direction;

    /**
     * Bank bit 2
     * @var integer
     *
     * @ORM\Column(name="bit2", type="integer", nullable=true)
     */
    protected $bit2;

    /**
     * Change direction of bit 2
     * @var string
     *
     * @ORM\Column(name="bit2_direction", type="string", nullable=true)
     */
    protected $bit2_direction;

    /**
     * Bank bit 3
     * @var integer
     *
     * @ORM\Column(name="bit3", type="integer", nullable=true)
     */
    protected $bit3;

    /**
     * Change direction of bit 3
     * @var string
     *
     * @ORM\Column(name="bit3_direction", type="string", nullable=true)
     */
    protected $bit3_direction;

    /**
     * Bank bit 4
     * @var integer
     *
     * @ORM\Column(name="bit4", type="integer", nullable=true)
     */
    protected $bit4;

    /**
     * Change direction of bit 4
     * @var string
     *
     * @ORM\Column(name="bit4_direction", type="string", nullable=true)
     */
    protected $bit4_direction;

    /**
     * Bank bit 5
     * @var integer
     *
     * @ORM\Column(name="bit5", type="integer", nullable=true)
     */
    protected $bit5;

    /**
     * Change direction of bit 5
     * @var string
     *
     * @ORM\Column(name="bit5_direction", type="string", nullable=true)
     */
    protected $bit5_direction;

    /**
     * Bank bit 6
     * @var integer
     *
     * @ORM\Column(name="bit6", type="integer", nullable=true)
     */
    protected $bit6;

    /**
     * Change direction of bit 6
     * @var string
     *
     * @ORM\Column(name="bit6_direction", type="string", nullable=true)
     */
    protected $bit6_direction;

    /**
     * Bank bit 7
     * @var integer
     *
     * @ORM\Column(name="bit7", type="integer", nullable=true)
     */
    protected $bit7;

    /**
     * Change direction of bit 7
     * @var string
     *
     * @ORM\Column(name="bit7_direction", type="string", nullable=true)
     */
    protected $bit7_direction;
}