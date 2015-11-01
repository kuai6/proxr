<?php
declare (ticks = 1);
namespace Application\Daemon;

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

    public function init()
    {
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        while (1) {
            //чтение информации из сокета, при получении инфо постылаем в очередь сообщение
            sleep(2);
        }
    }
}
