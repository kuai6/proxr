<?php

namespace Application\Entity\Periphery;

use Application\Entity\Bank;
use Application\Entity\Device;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PeripheryUnit
 * @package Application\Entity\Periphery
 *
 * @ORM\Entity(repositoryClass="Application\EntityRepository\Periphery")
 * @ORM\Table(name="periphery_unit")
*/
class PeripheryUnit
{
    /**
     * Periphery unit id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * Periphery type
     * @var PeripheryType
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Periphery\PeripheryType"), fetch="LAZY")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
    */
    private $type;

    /**
     * Connected device
     * @var Device
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Device"))
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=false)
    */
    private $device;

    /**
     * Connected bank
     * @var Bank
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Bank")
     * @ORM\JoinColumn(name="bank_id", referencedColumnName="id", nullable=false)
    */
    private $bank;

    /**
     * Connected bit
     * @var integer
     *
     * @ORM\Column(name="bit", type="integer")
    */
    private $bit;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return PeripheryType
     */
    public function getType(): PeripheryType
    {
        return $this->type;
    }

    /**
     * @return Device
     */
    public function getDevice(): Device
    {
        return $this->device;
    }

    /**
     * @return Bank
     */
    public function getBank(): Bank
    {
        return $this->bank;
    }

    /**
     * @return int
     */
    public function getBit(): int
    {
        return $this->bit;
    }

}