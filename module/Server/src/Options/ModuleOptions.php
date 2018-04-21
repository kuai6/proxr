<?php

namespace Server\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 * @package Server\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    private $bindingIp = '10.10.10.1';

    /**
     * @var integer
     */
    private $bindingPort = 9999;

    /**
     * Number of milliseconds
     * @var integer
     */
    private $heartbeat = 5000;

    /**
     * Number of milliseconds
     * @var integer
     */
    private $keepAliveTimeout = 15000;

    /**
     * Number of milliseconds
     * @var integer
     */
    private $maxRequestFrequency = 500;

    /**
     * @return string
     */
    public function getBindingIp()
    {
        return $this->bindingIp;
    }

    /**
     * @param string $bindingIp
     * @return $this
     */
    public function setBindingIp($bindingIp)
    {
        $this->bindingIp = $bindingIp;
        return $this;
    }

    /**
     * @return int
     */
    public function getBindingPort()
    {
        return $this->bindingPort;
    }

    /**
     * @param int $bindingPort
     * @return $this
     */
    public function setBindingPort($bindingPort)
    {
        $this->bindingPort = $bindingPort;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeartbeat()
    {
        return $this->heartbeat;
    }

    /**
     * @param int $heartbeat
     * @return $this
     */
    public function setHeartbeat($heartbeat)
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }

    /**
     * @return int
     */
    public function getKeepAliveTimeout()
    {
        return $this->keepAliveTimeout;
    }

    /**
     * @param int $keepAliveTimeout
     * @return $this
     */
    public function setKeepAliveTimeout($keepAliveTimeout)
    {
        $this->keepAliveTimeout = $keepAliveTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRequestFrequency()
    {
        return $this->maxRequestFrequency;
    }

    /**
     * @param int $maxRequestFrequency
     * @return $this
     */
    public function setMaxRequestFrequency($maxRequestFrequency)
    {
        $this->maxRequestFrequency = $maxRequestFrequency;
        return $this;
    }
}