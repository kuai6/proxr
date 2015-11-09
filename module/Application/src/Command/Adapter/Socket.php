<?php

namespace Application\Command\Adapter;

/**
 * Class Socket
 * @package Application\Command\Adapter
 */
class Socket implements AdapterInterface
{
    protected $resource;

    protected $options;

    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     */
    public function setOptions($options = [])
    {
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
        $this->resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        return socket_connect($this->resource, $ip, $port);
    }

    /**
     * Send request to the remote server
     *
     * @param string $body
     * @return bool
     */
    public function write($body)
    {
        return socket_write($this->resource, $body) !== false;
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        return socket_read($this->resource, 2048);
    }

    /**
     * Close the connection to the server
     */
    public function close()
    {
        socket_close($this->resource);
    }
}
