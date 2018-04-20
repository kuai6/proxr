<?php

namespace Application\Daemon;
use Application\Service\Daemon;
use Application\Service\UdpService;
use Application\ServiceManager\ServiceManagerAwareTrait;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class UdpDaemon
 * @package Application\Daemon
 */
class UdpDaemon extends AbstractLoopDaemon implements  ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    protected $processTitle = 'UdpDaemon';

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * @var integer
     */
    protected $processIdle = 0; // бесконечно

    /**
     * @var string
     */
    protected $logPath = './data/logs/udp';
    /**
     * @var string
     */
    protected $processPath  = './data/logs/udp';

    protected $childNumber = 1;


    /**
     * @var resource
     */
    private $sock;

    /**
     * @var UdpService
     */
    private $udpService;

    /**
     *
     */
    public function init()
    {
        $application = Application::init(
            require __DIR__ . '/../../../../config/application.config.php'
        );
        $this->setServiceManager($application->getServiceManager());

        $this->udpService = $this->serviceManager->get(UdpService::class);

        //Create a UDP socket
        if(!($this->sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }
        // Bind the source address
        if(!socket_bind($this->sock, "0.0.0.0" , 9999))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Could not bind socket : [$errorcode] $errormsg \n");
        }
    }


    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {

        while (true) {
            try {
                $remote_ip = $remote_port = null;
                //Receive some data
                $len = socket_recvfrom($this->sock, $buf, 512, 0, $remote_ip, $remote_port);
                if ($len > 0) {
                    $this->log("Received %s\n", $buf);
                    // resolve message and trigger event into bus
                    $this->udpService->resolveCommand($buf, $remote_ip, $remote_port);
                }
            } catch (\Exception $e) {
                $this->log('%s %s', get_class($e), $e->getMessage());
                //throw $e;
            }
        }



    }
}