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
        $this->logger->info("Running UDP server");

        //Create a UDP socket
        if(!($this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            throw new \RuntimeException("Couldn't create socket: [$errorcode] $errormsg");
        }

        //socket_set_option($this->sock, SOL_SOCKET, SO_BROADCAST, 1);


        // Bind the source address
        if(!socket_bind($this->sock, 0 , $this->moduleOptions->getBindingPort()))
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
                $len = socket_recvfrom($this->sock, $buf, 2048, 0, $remote_ip, $remote_port);
                if ($len > 0) {
                    // resolve message and trigger event into bus
                    $this->handle(trim($buf), $remote_ip, $remote_port);
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
        if (strlen($data) < 12) {
            $this->logger->err("Message corrupted");
            return;
        }

        $command = substr($data, 0,4);
        $serialNumber = substr($data, 4,8);
        $payload = substr($data, 12);

        $this->logger->debug(sprintf("Received %s from %s:%s", $command, $ip, $port));

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
    public function send($ip, $port, $command, $data)
    {
        $s = null;
        try {
            $this->logger->debug(sprintf("Sending %s to %s:%s", $command, $ip, $port));

            //Send the message
            if (!socket_sendto($this->sock, $data, strlen($data), 0, $ip, $port)) {
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