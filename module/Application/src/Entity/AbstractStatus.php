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
     * Statis Id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(name="id")
     */
    protected $id;

    /**
     * Status code
     * @var string
     *
     * @ORM\Column(name="code", nullable=false)
     */
    protected $code;
}
