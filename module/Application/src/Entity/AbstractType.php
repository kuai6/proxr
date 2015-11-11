<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="type")
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 *
 * Class AbstractType
 * @package Application\Entity
 */
class AbstractType
{
    /**
     * Type id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * Type code
     * @var string
     *
     * @ORM\Column(name="code", nullable=false)
     */
    protected $code;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
