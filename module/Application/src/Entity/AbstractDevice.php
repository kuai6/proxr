<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="device")
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 *
 * Class AbstractDevice
 * @package Application\Entity
 */
class AbstractDevice
{
    /**
     * Device Id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
