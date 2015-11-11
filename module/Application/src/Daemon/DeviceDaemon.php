<?php
declare (ticks = 1);
namespace Application\Daemon;

use Application\Command\AbstractCommand;
use Application\Command\Adapter\Socket;
use Application\Command\ContactClosure;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Демон для платы. запускается и настраевается системой
 *
 * Class DeviceDaemon
 * @package Application\Daemon
 */
class DeviceDaemon extends AbstractLoopDaemon implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    protected $processTitle = 'DeviceDaemon-01';

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * @var integer
     */
    protected $processIdle = 0; // бесконечно

    /**
     * @var string
     */
    protected $logPath = './data/logs/device';
    /**
     * @var string
     */
    protected $processPath  = './data/logs/device';

    /** @var  AbstractCommand */
    protected $command;

    /** @var  string */
    protected $commandAction;

    protected $device;

    public function init()
    {
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        $command = new ContactClosure();
        $command->setAdapter(new Socket());
        $command->getAdapter()->connect($this->getDevice()->getIp(), $this->getDevice()->getPort());
        $this->setCommand($command);
        $this->setCommandAction('getAllStatuses');
        while (true) {
            try {
                $this->getCommand()->{$this->getCommandAction()}();
            } catch (\Exception $e) {
                $this->err("Exception: %s Message: %s", get_class($e), $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * @return AbstractCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param AbstractCommand $command
     * @return DeviceDaemon
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommandAction()
    {
        return $this->commandAction;
    }

    /**
     * @param string $commandAction
     * @return DeviceDaemon
     */
    public function setCommandAction($commandAction)
    {
        $this->commandAction = $commandAction;
        return $this;
    }

    /**
     * PHP 5 introduces a destructor concept similar to that of other object-oriented languages, such as C++.
     * The destructor method will be called as soon as all references to a particular object are removed or
     * when the object is explicitly destroyed or in any order in shutdown sequence.
     *
     * Like constructors, parent destructors will not be called implicitly by the engine.
     * In order to run a parent destructor, one would have to explicitly call parent::__destruct() in the destructor body.
     *
     * Note: Destructors called during the script shutdown have HTTP headers already sent.
     * The working directory in the script shutdown phase can be different with some SAPIs (e.g. Apache).
     *
     * Note: Attempting to throw an exception from a destructor (called in the time of script termination) causes a fatal error.
     *
     * @return void
     * @link http://php.net/manual/en/language.oop5.decon.php
     */
    public function __destruct()
    {
        if ($this->getCommand()) {
            $this->getCommand()->getAdapter()->close();
        }
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     * @return DeviceDaemon
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }
}
