<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="status")
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 *
 * Class AbstractStatus
 * @package Application\Entity
 */
class AbstractStatus
{
    /**
     * Status Id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * Status code
     * @var string
     *
     * @ORM\Column(name="code", nullable=false)
     */
    protected $code;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
