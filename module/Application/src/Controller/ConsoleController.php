<?php
namespace Application\Controller;

use Application\Daemon\ContactClosureDaemon;
use Application\Daemon\MainDaemon;
use Application\Daemon\TestDaemon;
use Application\Entity\Device;
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


    public function daemonAction()
    {
        /** @var ConsoleRequest $request */
        $request = $this->getRequest();
        $options = $request->getParams()->toArray();

        $daemons = [
            'main' => [
                'class' => MainDaemon::class
            ],
            'device' => [
                'class' => ContactClosureDaemon::class,
            ],
        ];

        if (!in_array($options['daemonName'], array_keys($daemons))) {
            throw new \Exception(sprintf('Daemon with name %s not found', $options['daemonName']));
        }

        $daemonOptions = $daemons[$options['daemonName']];
        $daemon = $this->getServiceLocator()->get($daemonOptions['class']);
        $daemon->$options['command']();
    }

    /**
     * Test daemon Action
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
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entity_manager.orm_default');
        /** @var Device $device */
        $device = $entityManager->getRepository(Device::class)->findOneBy(['id' => 1]);
        /** @var ContactClosureDaemon $daemon */
        $daemon = $this->getServiceLocator()->get(ContactClosureDaemon::class);
        $daemon->setDevice($device);
        $daemon->setLogPath('./data/logs/contactClosure');
        $daemon->setProcessPath('./data/logs/contactClosure');
        $daemon->setProcessTitle('contactClosureDevice');
        $daemon->start();
    }
}
