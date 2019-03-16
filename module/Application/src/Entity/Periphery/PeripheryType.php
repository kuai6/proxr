<?php

namespace Application\Entity\Periphery;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PeripheryType
 * @package Application\Entity\Periphery
 *
 * @ORM\Entity(repositoryClass="Application\EntityRepository\PeripheryType")
 * @ORM\Table(name="periphery_type")
 */
class PeripheryType
{
    /**
     * Type Id
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * Name
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
    */
    private $name;

    /**
     * Description
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
