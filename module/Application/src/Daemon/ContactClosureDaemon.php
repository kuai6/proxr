<?php
declare (ticks = 1);
namespace Application\Daemon;

use Application\Command\AbstractCommand;
use Application\Entity\Device;
use Application\Service\Daemon;
use Application\ServiceManager\ServiceManagerAwareTrait;
use Kuai6\Queue\Exchange;
use Kuai6\Queue\Message;
use Kuai6\Queue\Server;
use Kuai6\Queue\ServerFactory;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Демон для платы. запускается и настраевается системой
 *
 * Class ContactClosureDaemon
 * @package Application\Daemon
 */
class ContactClosureDaemon extends AbstractLoopDaemon implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    protected $processTitle = 'contactClosureDevice';

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * @var integer
     */
    protected $processIdle = 0; // бесконечно

    /**
     * @var string
     */
    protected $logPath = './data/logs/contactClosure';
    /**
     * @var string
     */
    protected $processPath  = './data/logs/contactClosure';

    /** @var  AbstractCommand */
    protected $command;

    /** @var  string */
    protected $commandAction;

    /** @var  Device */
    protected $device;

    /** @var  Server */
    protected $queueServer;

    /** @var  Message */
    protected $queueMessage;

    /** @var  string */
    protected $queueMessageRoutingKey;

    /** @var  Exchange */
    protected $queueExchange;

    /** @var array  */
    public $statuses = [];

    /**
     * @throws \Exception
     */
    public function init()
    {
        try {
            /** @var Server $queueServer */
            $queueServer = $this->getServiceManager()->get(ServerFactory::class);
            $this->setQueueServer($queueServer);
            $this->setQueueMessageRoutingKey(sprintf('app.daemon.%s.event',
                $this->getDevice()->getName()
            ));
            $this->setQueueMessage(new Message());
        } catch (\Exception $e) {
            $this->log('%s %s', get_class($e), print_r($e->getTraceAsString(), true));
            throw $e;
        }
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        while (true) {
            try {
                $this->getServiceManager()->get(Daemon::class)->contactClosureDaemonCycle($this);
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
     * @return ContactClosureDaemon
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
     * @return ContactClosureDaemon
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
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param Device $device
     * @return ContactClosureDaemon
     */
    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * @param array $statuses
     * @return ContactClosureDaemon
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
        return $this;
    }

    /**
     * @return Message
     */
    public function getQueueMessage()
    {
        return $this->queueMessage;
    }

    /**
     * @param Message $queueMessage
     * @return ContactClosureDaemon
     */
    public function setQueueMessage($queueMessage)
    {
        $this->queueMessage = $queueMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueueMessageRoutingKey()
    {
        return $this->queueMessageRoutingKey;
    }

    /**
     * @param mixed $queueMessageRoutingKey
     * @return ContactClosureDaemon
     */
    public function setQueueMessageRoutingKey($queueMessageRoutingKey)
    {
        $this->queueMessageRoutingKey = $queueMessageRoutingKey;
        return $this;
    }

    /**
     * @return Server
     */
    public function getQueueServer()
    {
        return $this->queueServer;
    }

    /**
     * @param Server $queueServer
     * @return ContactClosureDaemon
     */
    public function setQueueServer($queueServer)
    {
        $this->queueServer = $queueServer;
        return $this;
    }

    /**
     * @return Exchange
     */
    public function getQueueExchange()
    {
        return $this->queueExchange;
    }

    /**
     * @param Exchange $queueExchange
     * @return ContactClosureDaemon
     */
    public function setQueueExchange($queueExchange)
    {
        $this->queueExchange = $queueExchange;
        return $this;
    }
}
