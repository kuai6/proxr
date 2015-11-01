<?php
namespace Application\Controller;

use Application\Daemon\TestDaemon;
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

    /**
     * Test daemon Action
     */
    public function testAction()
    {
        /** @var TestDaemon $daemon */
        $daemon = $this->getServiceLocator()->get('Application\Daemon\TestDaemon');
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
}
