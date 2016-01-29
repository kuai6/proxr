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
     * @throws Exception\RuntimeException
     */
    public function connect($ip, $port)
    {
        $this->resource = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->resource === false) {
            throw new Exception\RuntimeException('Unable to create socket');
        }
        $result = @socket_connect($this->resource, $ip, $port);
        if ($result === false) {
            throw new Exception\RuntimeException('Unable to connect socket');
        }
        @socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 1, 'usec' => 0]);
        @socket_set_option($this->resource, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 1, 'usec' => 0]);
        @socket_set_timeout($this->resource, 1);
    }

    /**
     * Send request to the remote server
     *
     * @param string $body
     * @return bool
     * @throws Exception\RuntimeException
     */
    public function write($body)
    {
        if (@socket_write($this->resource, $body) === false) {
            throw new Exception\RuntimeException('Unable to write socket');
        }
        return true;
    }

    /**
     * Read response from server
     * @return string
     * @throws Exception\RuntimeException
     */
    public function read()
    {
        $result = @socket_read($this->resource, 255);
        if ($result === false) {
            $errorCode = @socket_last_error($this->resource);
            $errorMessage = @socket_strerror($errorCode);
            throw new Exception\RuntimeException(sprintf('Unable to read from socket [%s]:%s', $errorCode, $errorMessage));
        }
        return $result;
    }

    /**
     * Close the connection to the server
     */
    public function close()
    {
        @socket_close($this->resource);
    }
}
