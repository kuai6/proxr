<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bank")
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 *
 * Class AbstractBank
 * @package Application\Entity
 */
class AbstractBank
{
    /**
     * Bank Id
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
