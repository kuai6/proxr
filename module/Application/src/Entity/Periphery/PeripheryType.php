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
     * Icon path
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $icon;

    /**
     * Inputs count
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $inputs;

    /**
     * Outputs count
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $outputs;

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

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return int
     */
    public function getInputs(): int
    {
        return $this->inputs;
    }

    /**
     * @param int $inputs
     */
    public function setInputs(int $inputs): void
    {
        $this->inputs = $inputs;
    }

    /**
     * @return int
     */
    public function getOutputs(): int
    {
        return $this->outputs;
    }

    /**
     * @param int $outputs
     */
    public function setOutputs(int $outputs): void
    {
        $this->outputs = $outputs;
    }
}
