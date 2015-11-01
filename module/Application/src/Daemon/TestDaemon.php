<?php
declare (ticks = 1);
namespace Application\Daemon;

class TestDaemon extends AbstractLoopDaemon
{
    protected $processTitle = 'TestDaemon';

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * @var integer
     */
    protected $processIdle = 0; // бесконечно

    /**
     * @var string
     */
    protected $logPath = './data/logs/test';
    /**
     * @var string
     */
    protected $processPath  = './data/logs/test';

    public function init()
    {
    }

    /**
     * Реализация одного цикла демона
     */
    public function cycle()
    {
        while (1) {
            $memoryUsage = memory_get_usage(true)/1024;
            $this->log('memory usage: %s kb', $memoryUsage);
            sleep(2);
        }
    }
}
