<?php

namespace Application\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ServiceManagerAwareTrait
 * @package Application\ServiceManager
 */
trait ServiceManagerAwareTrait
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager = null;

    /**
     * Set service locator
     *
     * @param ServiceManager $serviceManager
     * @return mixed
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
