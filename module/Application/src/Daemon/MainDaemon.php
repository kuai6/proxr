<?php
declare (ticks = 1);
namespace Application\Daemon;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Демон системы. Смотрит на очедередь. при получении какого-либо сообщания бросает соотвтевующее событие, которое ловится системой.
 *
 * Class MainDaemon
 * @package Application\Daemon
 */
class MainDaemon extends AbstractLoopDaemon implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

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

    public function init()
    {
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        while (1) {
            //consume на очередь.
            sleep(2);
        }
    }
}
