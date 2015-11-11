<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * Class Bank
 * @package Application\Entity
 */
class Bank extends AbstractBank
{
    /**
     * Device
     * @var \Application\Entity\Device
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Device", fetch="LAZY", inversedBy="banks")
     * @ORM\JoinColumn(name="deviceId", referencedColumnName="id", nullable=false)
     */
    protected $device;

    /**
     * Bank type
     * @var \Application\Entity\BankType
     *
     * @ORM\Column(name="typeId")
     * @ORM\ManyToOne(targetEntity="\Application\Entity\BankType", fetch="LAZY")
     */
    protected $type;

    /**
     * Bank name in device, e.q 1,2,3 etc
     * @var integer
     * @ORM\Column(name="name", type="smallint")
     */
    protected $name;


    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     * @return Bank
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return BankType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param BankType $type
     * @return Bank
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
