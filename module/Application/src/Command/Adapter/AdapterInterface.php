<?php

namespace Application\Command\Adapter;

/**
 * Interface AdapterInterface
 * @package Application\Command\Adapter
 */
interface AdapterInterface
{
    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     */
    public function setOptions($options = []);

    /**
     * Connect to the remote server
     *
     * @param string  $ip
     * @param int     $port
     * @return bool
     */
    public function connect($ip, $port);

    /**
     * Send request to the remote server
     *
     * @param string        $body
     * @return bool
     */
    public function write($body);

    /**
     * Read response from server
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server
     */
    public function close();
}
