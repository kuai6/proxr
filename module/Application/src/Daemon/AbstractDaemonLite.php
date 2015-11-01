<?php

namespace Application\Daemon;

use Application\Options\DaemonOptions;
use Application\Storage\StorageFactory;
use Zend\Cache\Storage\StorageInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Class AbstractDaemonLite
 * @package Application\Daemon
 */
abstract class AbstractDaemonLite
{
    /**
     * @var DaemonOptions
     */
    protected $options;

    /**
     * Может принимать значения true или false.
     * Если в екгу - демон должен остановиться.
     * @var bool
     */
    protected $needStop = false;

    /**
     * @var array
     */
    protected $connection = [];

    /**
     * @var StorageInterface
     */
    protected $storage;

    /** Действие выполнено */
    const RESPONSE_DONE = 'done';

    /** Действие находится в работе */
    const RESPONSE_PROCESSING = 'processing';


    /**
     * @param array $options
     * @throws
     */
    public function __construct(array $options = [])
    {
        try {
            if (substr_count(PHP_VERSION, 0, 3) === 'WIN') {
                throw new Exception\RuntimeException('Should only be run on the UNIX-like operating systems.');
            }
            $this->setOptions($options);
        } catch (\Exception $e) {
            $this->log($e);
        }
    }

    /**
     * Останавливаем процесс демона
     */
    public function stop()
    {
        if (!file_exists($this->getOptions()->getPidFile())) {
            $this->log('Демон не запущен', 'info');
            exit(1);
        }
        $pid = (int)(file_get_contents($this->getOptions()->getPidFile()));
        $this->log(sprintf('Остановка процесса %s', $pid), 'info');

        for ($i=0; $i<3; $i++) {
            if (file_exists($this->getOptions()->getPidFile())) {
                posix_kill($pid, SIGTERM);
                sleep(1);
            } else {
                break;
            }
        }
    }

    /**
     * Принудительно останавливаем процесс.
     */
    public function kill()
    {
        if (!file_exists($this->getOptions()->getPidFile())) {
            $this->log('Демон не запущен или нет pid файла', 'info');
            exit(1);
        }
        $pid = (int)(file_get_contents($this->getOptions()->getPidFile()));
        $this->log(sprintf('Принудительная остановка процесса %s', $pid), 'info');
        posix_kill($pid, SIGKILL);
        unlink($this->getOptions()->getPidFile());
    }

    /**
     * Запускаем процесс демона
     */
    public function start()
    {
        try {
            if (file_exists($this->getOptions()->getPidFile())) {
                throw new Exception\RuntimeException("Процесс с pid-файлом {$this->getOptions()->getPidFile()} уже запущен");
            }
            $pid = posix_getpid();
            file_put_contents($this->getOptions()->getPidFile(), $pid,  LOCK_EX | LOCK_NB);
            $this->log(sprintf('Запуск процесса %s', $pid), 'info');
            pcntl_signal(SIGTERM, [$this, 'handleSignal']);
            pcntl_signal(SIGHUP, [$this, 'handleSignal']);
            $storage = StorageFactory::create($this->getOptions()->getStorage());
            $this->setStorage($storage);
            $this->init();
            $this->run();
        } catch (\Exception $e) {
            $this->log($e);
        }
    }

    /**
     * Непосредственно выполняем действия
     */
    protected function run()
    {
        $start = microtime(true);
        $countIterations = $this->getOptions()->getCountIterations();
        if ($countIterations === 0) {
            throw new Exception\RuntimeException('Нельзя, чтобы количество итераций равнялось 0');
        }
        $i = 0;
        while (!$this->needStop) {
            $i++;
            $this->iteration();

            if ($countIterations && $i >= $countIterations) {
                $this->log(
                    sprintf('Завершение работы демона. Количество итераций %s достигнуто.', $countIterations), 'info');
                $this->log(sprintf('Время работы демона %s', (microtime(true) - $start)), 'info');
                break;
            }
            $memoryUsage = memory_get_usage(true)/1024;
            if ($memoryUsage && $memoryUsage > $this->getOptions()->getMemoryLimit()) {
                $this->log(sprintf('Завершение работы демона. Превышение допустимого лимита памяти %s. ',  $this->getOptions()->getMemoryLimit()), 'info');
                $this->log(sprintf('Время работы демона %s', (microtime(true) - $start)), 'info');
                break;
            }
            if ($this->getOptions()->getSleepTime()) {
                usleep((int)$this->getOptions()->getSleepTime());
            }
            if ($this->getOptions()->getLifeTime() && (microtime(true) - $start) >= $this->getOptions()->getLifeTime()) {
                $this->log(sprintf('Завершение работы демона. Превышение допустимого времени жизни процесса %s.',
                round($this->getOptions()->getLifeTime())), 'info');
                $this->log(sprintf('Время работы демона %s', (microtime(true) - $start)), 'info');
                break;
            }
        }
        unlink($this->getOptions()->getPidFile());
    }

    /**
     * Функция запускающая итерацию
     * и выполняющая непосредственно действия.
     * @return mixed
     */
    abstract protected function iteration();

    /**
     * Функция инициализации всего что нужно.
     * @return mixed
     */
    protected function init()
    {
    }

    /**
     * Логгирование действий демона
     * @param $message
     * @param string $type
     */
    protected function log($message, $type = 'error')
    {
        $options = $this->getOptions();

        $logger = new Logger();

        $writer = new Stream('php://output');
        $logger->addWriter($writer);

        $writer = new Stream($options->getLogFile());
        $logger->addWriter($writer);

        switch ($type) {
            case 'debug':
                $logger->debug($message);
                break;
            case 'info':
                $logger->info($message);
                break;
            case 'error':
                $logger->err($message);
                break;
        }
    }

    /**
     * @param int $signal
     */
    public function handleSignal($signal)
    {
        switch ($signal) {
            case SIGTERM:
            case SIGHUP:
                $this->log('SIG: ' . $signal, 'info');
                $this->setNeedStop(true);
                break;

            case SIGUSR1:
                echo 1;exit;
                break;

            default:
                // Ignore all other signals
                break;
        }
    }

    /**
     * Функция получения опций демона
     * @return DaemonOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Функция с помощью которой можно установить опции демона
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = new DaemonOptions($options);
        return $this;
    }

    /**
     * @param $key
     * @param $val
     * @throws Exception\RuntimeException
     */
    public function setOption($key, $val)
    {
        if (!property_exists($this->getOptions(), $key)) {
            throw new Exception\RuntimeException(sprintf('Свойство %s не найдено в классе %s', $key, get_class($this->getOptions())));
        }
        $setterName = 'set' . ucfirst($key);
        if (!method_exists($this->getOptions(), $setterName)) {
            throw new Exception\RuntimeException(sprintf('Метод %s не найден в классе %s', $key, get_class($this->getOptions())));
        }
        $this->getOptions()->$setterName($val);
    }

    /**
     * @return boolean
     */
    public function isNeedStop()
    {
        return $this->needStop;
    }

    /**
     * @param boolean $needStop
     * @return $this
     */
    public function setNeedStop($needStop)
    {
        $this->needStop = $needStop;
        return $this;
    }

    /**
     * @return array
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $connection
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param StorageInterface $storage
     * @return $this
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }
}
