<?php

namespace Server\Service;

use Server\Event\IncomeEvent;
use Server\Options\ModuleOptions;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;

/**
 * Class ServerService
 * @package Server\Service
 */
class ServerService
{

    const COMMAND_HELLO = 'HELO';
    const COMMAND_PING  = 'PING';
    const COMMAND_PONG  = 'PONG';
    const COMMAND_DATA  = 'DATA';
    const COMMAND_CONF  = 'CONF';


    /**
     * @var ModuleOptions
     */
    private $moduleOptions;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var resource
     */
    private $sock;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ServerService constructor.
     * @param ModuleOptions $moduleOptions
     */
    public function __construct(ModuleOptions $moduleOptions, EventManagerInterface $eventManager, LoggerInterface $logger)
    {
        $this->moduleOptions = $moduleOptions;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->sock !== null) {
            socket_close($this->sock);
        }
    }

    /**
     *
     * @throws \RuntimeException
     */
    public function run()
    {
        //Create a UDP socket
        if(!($this->sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \RuntimeException("Couldn't create socket: [$errorcode] $errormsg");
        }
        // Bind the source address
        if(!socket_bind($this->sock, $this->moduleOptions->getBindingIp() , $this->moduleOptions->getBindingPort()))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            throw new \RuntimeException("Could not bind socket : [$errorcode] $errormsg");
        }

        //cycle
        while(true) {
            try {
                $remote_ip = $remote_port = null;
                //Receive some data
                $len = socket_recvfrom($this->sock, $buf, 512, 0, $remote_ip, $remote_port);
                if ($len > 0) {
                    $this->logger->debug(sprintf("Received %s", $buf));
                    // resolve message and trigger event into bus
                    $this->handle($buf, $remote_ip, $remote_port);
                }
            } catch (\Exception $e) {
                $this->logger->err(sprintf('%s %s', get_class($e), $e->getMessage()));
            }
        }
    }


    /**
     * @param $data
     * @param $ip
     * @param $port
     * @throws \Zend\EventManager\Exception\InvalidArgumentException
     */
    public function handle($data, $ip, $port)
    {
        $command = substr($data, 0,4);
        $serialNumber = substr($data, 4,8);
        $payload = substr($data, 12);

        $event = new IncomeEvent();
        $event->setName(sprintf('income.event.%s', $command));
        $event->setParams([
            'command'   => $command,
            'serial'    => $serialNumber,
            'ip'        => $ip,
            'port'      => $port,
            'payload'   => $payload,
        ]);
        $this->eventManager->trigger($event);
    }

    /**
     * @param $ip
     * @param $port
     * @param $data
     */
    public function send($ip, $port, $data)
    {
        $s = null;
        try {
            if (!($s = socket_create(AF_INET, SOCK_DGRAM, 0))) {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                throw new \RuntimeException("Couldn't create socket: [$errorcode] $errormsg");
            }

            //Send the message
            if (!socket_sendto($s, $data, strlen($data), 0, $ip, $port)) {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                throw new \RuntimeException("Could not send data: [$errorcode] $errormsg");
            }
        } catch (\Exception $e) {
            $this->logger->err(sprintf('%s %s', get_class($e), $e->getMessage()));
        } finally {
            if (null !== $s) {
                socket_close($s);
            }
        }
    }


}