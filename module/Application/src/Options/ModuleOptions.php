<?php
/**
 * Created by PhpStorm.
 * User: kuai6
 * Date: 12.03.19
 * Time: 13:22
 */

namespace Application\Options;


use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 * @package Application\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $modulePath;

    /**
     * @return string
     */
    public function getModulePath(): string
    {
        return $this->modulePath;
    }

    /**
     * @param string $modulePath
     */
    public function setModulePath(string $modulePath): void
    {
        $this->modulePath = $modulePath;
    }
}