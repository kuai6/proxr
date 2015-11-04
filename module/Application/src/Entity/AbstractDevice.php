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
     * @var integer
     * @ORM\Id()
     * @ORM\Column(name="id")
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
