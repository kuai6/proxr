<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\EntityRepository\Bank")
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
     * @ORM\Column(name="bankTypeId")
     * @ORM\ManyToOne(targetEntity="\Application\Entity\BankType", fetch="LAZY")
     */
    protected $bankType;

    /**
     * Bank name in device, e.q 1,2,3 etc
     * @var integer
     *
     * @ORM\Column(name="name", type="smallint")
     */
    protected $name;

    /**
     * Bank bit 0
     * @var integer
     *
     * @ORM\Column(name="bit0", type="integer")
     */
    protected $bit0 = 0;

    /**
     * Bank bit 1
     * @var integer
     *
     * @ORM\Column(name="bit1", type="integer")
     */
    protected $bit1 = 0;

    /**
     * Bank bit 2
     * @var integer
     *
     * @ORM\Column(name="bit2", type="integer")
     */
    protected $bit2 = 0;

    /**
     * Bank bit 3
     * @var integer
     *
     * @ORM\Column(name="bit3", type="integer")
     */
    protected $bit3 = 0;

    /**
     * Bank bit 4
     * @var integer
     *
     * @ORM\Column(name="bit4", type="integer")
     */
    protected $bit4 = 0;

    /**
     * Bank bit 5
     * @var integer
     *
     * @ORM\Column(name="bit5", type="integer")
     */
    protected $bit5 = 0;

    /**
     * Bank bit 6
     * @var integer
     *
     * @ORM\Column(name="bit6", type="integer")
     */
    protected $bit6 = 0;

    /**
     * Bank bit 7
     * @var integer
     *
     * @ORM\Column(name="bit7", type="integer")
     */
    protected $bit7 = 0;

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
    public function getBankType()
    {
        return $this->bankType;
    }

    /**
     * @param BankType $bankType
     * @return Bank
     */
    public function setBankType($bankType)
    {
        $this->bankType = $bankType;
        return $this;
    }

    /**
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $name
     * @return Bank
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBit0()
    {
        return $this->bit0;
    }

    /**
     * @param mixed $bit0
     * @return Bank
     */
    public function setBit0($bit0)
    {
        $this->bit0 = $bit0;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit1()
    {
        return $this->bit1;
    }

    /**
     * @param int $bit1
     * @return Bank
     */
    public function setBit1($bit1)
    {
        $this->bit1 = $bit1;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit2()
    {
        return $this->bit2;
    }

    /**
     * @param int $bit2
     * @return Bank
     */
    public function setBit2($bit2)
    {
        $this->bit2 = $bit2;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit3()
    {
        return $this->bit3;
    }

    /**
     * @param int $bit3
     * @return Bank
     */
    public function setBit3($bit3)
    {
        $this->bit3 = $bit3;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit4()
    {
        return $this->bit4;
    }

    /**
     * @param int $bit4
     * @return Bank
     */
    public function setBit4($bit4)
    {
        $this->bit4 = $bit4;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit5()
    {
        return $this->bit5;
    }

    /**
     * @param int $bit5
     * @return Bank
     */
    public function setBit5($bit5)
    {
        $this->bit5 = $bit5;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit6()
    {
        return $this->bit6;
    }

    /**
     * @param int $bit6
     * @return Bank
     */
    public function setBit6($bit6)
    {
        $this->bit6 = $bit6;
        return $this;
    }

    /**
     * @return int
     */
    public function getBit7()
    {
        return $this->bit7;
    }

    /**
     * @param int $bit7
     * @return Bank
     */
    public function setBit7($bit7)
    {
        $this->bit7 = $bit7;
        return $this;
    }

    /**
     * Устанавливает биты из байта
     * @param $byte
     * @return $this
     */
    public function setByte($byte)
    {
        if (!is_int($byte)) {
            $byte = hexdec($byte);
        }
        $this->bit0 = ($byte & 1)  ==  1   ? 1 : 0;
        $this->bit1 = ($byte & 2)  ==  2   ? 1 : 0;
        $this->bit2 = ($byte & 4)  ==  4   ? 1 : 0;
        $this->bit3 = ($byte & 8)  ==  8   ? 1 : 0;
        $this->bit4 = ($byte & 16) ==  16  ? 1 : 0;
        $this->bit5 = ($byte & 32) ==  32  ? 1 : 0;
        $this->bit6 = ($byte & 64) ==  64  ? 1 : 0;
        $this->bit7 = ($byte & 128)==  128 ? 1 : 0;
        return $this;
    }

    /**
     * Возвращает байт в виде массива битов
     *
     * @return array
     */
    public function getByte()
    {
        return[
            $this->bit0,
            $this->bit1,
            $this->bit2,
            $this->bit3,
            $this->bit4,
            $this->bit5,
            $this->bit6,
            $this->bit7,
        ];
    }
}
