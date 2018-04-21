<?php

namespace Application\Command\Adapter;

/**
 * Class Event
 * @package Application\Command\Adapter
 */
class Event implements AdapterInterface
{
    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     */
    public function setOptions($options = [])
    {
        // TODO: Implement setOptions() method.
    }

    /**
     * Connect to the remote server
     *
     * @param string $ip
     * @param int $port
     * @return bool
     */
    public function connect($ip, $port)
    {
        // TODO: Implement connect() method.
    }

    /**
     * Send request to the remote server
     *
     * @param string $body
     * @return bool
     */
    public function write($body)
    {
        // TODO: Implement write() method.
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        // TODO: Implement read() method.
    }

    /**
     * Close the connection to the server
     */
    public function close()
    {
        // TODO: Implement close() method.
    }
}