<?php
declare (ticks = 1);
namespace Application\Daemon;

use Application\Service\Daemon;
use Application\ServiceManager\ServiceManagerAwareTrait;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Демон системы. Смотрит на очедередь. при получении какого-либо сообщания бросает соотвтевующее событие, которое ловится системой.
 *
 * Class MainDaemon
 * @package Application\Daemon
 */
class MainDaemon extends AbstractLoopDaemon implements  ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    protected $processTitle = 'MainDaemon';

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * @var integer
     */
    protected $processIdle = 0; // бесконечно

    /**
     * @var string
     */
    protected $logPath = './data/logs/main';
    /**
     * @var string
     */
    protected $processPath  = './data/logs/main';

    protected $childNumber = 1;

    /**
     *
     */
    public function init()
    {
        $application = Application::init(
            require __DIR__ . '/../../../../config/application.config.php'
        );
        $this->setServiceManager($application->getServiceManager());
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        /** @var Daemon $daemonService */
        $daemonService = $this->getServiceManager()->get(Daemon::class);

        while (true) {
            try {
                $daemonService->mainDaemonCycle();
            } catch (\Exception $e) {
                $this->log('%s %s', get_class($e), $e->getMessage());
                throw $e;
            }
        }
    }
}
