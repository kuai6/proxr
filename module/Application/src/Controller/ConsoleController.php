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
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;

/**
 * Class ConsoleController
 * @package Application\Controller
 */
class ConsoleController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Queue
     */
    private $queueService;

    private $testDaemon;


    private $daemonManager;

    /**
     * ConsoleController constructor.
     * @param array $config
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param Queue $queueService
     */
    public function __construct(array $config = [], \Doctrine\ORM\EntityManager $entityManager, Queue $queueService)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->queueService = $queueService;
    }

    /**
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        if (!($this->getRequest() instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
        return parent::onDispatch($e);
    }


    /**
     *
     * @throws \RuntimeException
     */
    public function daemonAction()
    {
        /** @var ConsoleRequest $request */
        $request = $this->getRequest();
        $options = $request->getParams()->toArray();

        $daemons = [
            'main' => [
                'class' => MainDaemon::class
            ],
            'contactClosureDevice' => [
                'class' => ContactClosureDaemon::class,
            ],
            'udp' => [
                'class' => UdpDaemon::class,
            ],
        ];

        if (!array_key_exists($options['daemonName'], $daemons)) {
            throw new \RuntimeException(sprintf('Daemon with name %s not found', $options['daemonName']));
        }

        $daemonOptions = $daemons[$options['daemonName']];
        $daemon = $this->getServiceLocator()->get($daemonOptions['class']);
        $daemon->{$options['command']}();
    }

    /**
     * Test daemon Action
     * @throws \Application\Daemon\Exception\RuntimeException
     */
    public function testAction()
    {
        /** @var TestDaemon $daemon */
        $daemon = $this->getServiceLocator()->get(TestDaemon::class);
        /** @var ConsoleRequest $request */
        $request = $this->getRequest();
        $options = $request->getParams()->toArray();
        $options['logPath'] = isset($options['logPath']) ? $options['logPath'] : $daemon->getLogPath();
        $daemon->setLogPath($options['logPath']);

        $options['processPath'] = isset($options['processPath']) ? $options['processPath'] : $daemon->getProcessPath();
        $daemon->setProcessPath($options['processPath']);

        switch ($options['command']) {
            case 'start':
                $daemon->start();
                break;
            case 'stop':
                $daemon->stop();
                break;
            case 'restart':
                $daemon->stop();
                $daemon->start();
                break;
        }
    }

    public function contactClosureDeviceDaemonAction()
    {
        /** @var Device $device */
        $device = $this->entityManager->getRepository(Device::class)->findOneBy(['id' => 1]);
        /** @var ContactClosureDaemon $daemon */
        $daemon = $this->getServiceLocator()->get(ContactClosureDaemon::class);
        $daemon->setDevice($device);
        $command = new ContactClosure();
        $command->setAdapter(new Socket());
        $command->getAdapter()->connect($device->getIp(), $device->getPort());
        $daemon->setCommand($command);
        $daemon->setCommandAction('getAllStatuses');

        $statuses = [];
        /** @var Bank $bank */
        foreach ($device->getBanks() as $bank) {
            $statuses[$bank->getName()] = $bank->getByte();
        }
        $daemon->setStatuses($statuses);
        $daemon->start();
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
