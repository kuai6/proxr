<?php
namespace Application\Controller;

use Application\Command\Adapter\Socket;
use Application\Command\ContactClosure;
use Application\Daemon\ContactClosureDaemon;
use Application\Daemon\MainDaemon;
use Application\Daemon\TestDaemon;
use Application\Daemon\UdpDaemon;
use Application\Entity\Bank;
use Application\Entity\Device;
use Application\Service\Queue;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;

/**
 * Class ConsoleController
 * @package Application\Controller
 */
class ConsoleController extends AbstractConsoleController
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Queue
     */
    private $queueService;

    /**
     * ConsoleController constructor.
     * @param array $config
     * @param Queue $queueService
     */
    public function __construct(array $config = [], Queue $queueService)
    {
        $this->config = $config;
        $this->queueService = $queueService;
    }

    /**
     * Инициализация всего окружения
     * @throws \Exception
     */
    public function systemInitAction()
    {
        if (array_key_exists('queue', $this->config)) {
            if (array_key_exists('exchanges', $this->config['queue'])) {
                $this->queueService->initExchanges($this->config['queue']['exchanges']);
            }
            if (array_key_exists('queues', $this->config['queue'])) {
                $this->queueService->initQueues($this->config['queue']['queues']);
            }
        }
    }
}
