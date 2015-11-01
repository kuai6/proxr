<?php

namespace Application\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Параметры для демона.
 *
 * Class DaemonOptions
 * @package Application\Options
 */
class DaemonOptions extends AbstractOptions
{
    /**
     * Файл в который будет писаться лог.
     * @var null|string
     */
    protected $logFile;

    /**
     * Файл в который будет писаться
     * лог ошибок. Может быть отдельным
     * от основного файла лога
     * @var null|string
     */
    protected $errorLogFile;

    /**
     * Количество итераций до смерти демона.
     * @var int
     */
    protected $countIterations;

    /**
     * Время сна процесса между итерациями
     * в микросекундах
     * @var int|null
     */
    protected $sleepTime;

    /**
     * Лимит памяти в KB при превышении
     * которого процесс перезапускается.
     * @var int
     */
    protected $memoryLimit;

    /**
     * @var string
     */
    protected $pidFile;

    /**
     * Время жизни процесса в сек
     * @var int
     */
    protected $lifeTime;

    /**
     * Конфигурация хранилища для обмена данными
     * между запускаемыми
     * и основным процессом
     * @var array
     */
    protected $storage;

    /**
     * @param array|\Traversable|null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->getErrorLogFile() === null) {
            $this->setErrorLogFile($this->getLogFile());
        }
    }

    /**
     * @return null|string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param null|string $logFile
     * @return $this
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountIterations()
    {
        return $this->countIterations;
    }

    /**
     * @param int $countIterations
     * @return $this
     */
    public function setCountIterations($countIterations)
    {
        $this->countIterations = $countIterations;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getErrorLogFile()
    {
        return $this->errorLogFile;
    }

    /**
     * @param null|string $errorLogFile
     * @return $this
     */
    public function setErrorLogFile($errorLogFile)
    {
        $this->errorLogFile = $errorLogFile;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSleepTime()
    {
        return $this->sleepTime;
    }

    /**
     * @param int|null $sleepTime
     * @return $this
     */
    public function setSleepTime($sleepTime)
    {
        $this->sleepTime = $sleepTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @param int $memoryLimit
     * @return $this
     */
    public function setMemoryLimit($memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
        return $this;
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return $this->pidFile;
    }

    /**
     * @param string $pidFile
     * @return $this
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = $pidFile;
        return $this;
    }

    /**
     * @return int
     */
    public function getLifeTime()
    {
        return $this->lifeTime;
    }

    /**
     * @param int $lifeTime
     * @return $this
     */
    public function setLifeTime($lifeTime)
    {
        $this->lifeTime = $lifeTime;
        return $this;
    }

    /**
     * @return array
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param array $storage
     * @return $this
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }
}
