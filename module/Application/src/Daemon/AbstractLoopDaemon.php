<?php

namespace Application\Daemon;

use Application\Log\Formatter\Sprintf;
use Zend\Console\Adapter\Posix;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Abstract loop process (for daemons)
 * Class AbstractLoopDaemon
 * @package Application\Daemon
 */
abstract class AbstractLoopDaemon
{
    /**
     * Метод без возвращаемого значения
     */
    const VOID_METHOD = 'void_method';

    /**
     * Метод с возвращаемым значением
     */
    const RETURN_METHOD = 'void_method';

    /**
     * Cycles interval (microseconds)
     *
     * @var integer
     */
    protected $interval = 1000000; // default 1 sec

    /**
     * Cycles limit
     * Критерий перезапуска
     *
     * @var integer
     */
    protected $cyclesLimit = null;

    /**
     * Executing time limit
     * Критерий перезапуска
     *
     * @var integer
     */
    protected $timeLimit = null;

    /**
     * Предела размера используемой памити (Кб)
     * @var null|int
     */
    protected $memoryLimit = null;

    /**
     * Количество фактических итераций
     * @var int
     */
    protected $cycleCount = 1;

    /**
     * Флаг требования остановки
     * @var bool
     */
    protected $stop = false;

    /**
     * Проверка на разветвление процесса.
     *
     * @var boolean
     */
    protected $isRunning = false;

    /**
     * Количество попыток проверки завершения работы ребенка
     * (фактически это количество сек)
     * @var int
     */
    protected $attemptToKillChild = 15;

    /**
     * Daemon start timestamp
     *
     * @var integer
     */
    protected $startTime;

    protected $params = [];

    /**
     * @var bool
     */
    protected $isChild = false;

    /**
     * Количество потоков
     *
     * @var integer
     */
    protected $childNumber = 1;

    ###################################
    ##      Daemon Monitoring
    ###################################
    /**
     * Время между проверками активности детей (мсек.)
     *
     * @var integer
     */
    protected $monitoringSleep = 1500;

    /**
     * Максимальное время бездействия процесса ребенка (мсек.)
     * Если значение равно 0 (ноль), то проверка не выполняется
     * (Примечание: должно быть больше $interval)
     *
     * @var integer
     */
    protected $processIdle = 5000;

    /**
     * Количество попыток прочитать pid-файл
     * (подозрительные проверки: файл может оказаться пустым; может прочитать данные не полностью).
     *
     * @var integer
     */
    protected $maxSuspiciousAttempts = 10;

    /**
     * Количество миллисекунд для ожидания завершения дочерних процессов (мсек)
     *
     * @var integer
     */
    protected $timeoutTERM = 3000;

    /**
     * Флаг остановки демона
     *
     * @var boolean
     */
    protected $needStop = false;

    ###################################
    ##      PID variables
    ###################################
    /**
     * PID текущего процесса
     *
     * @var null|int
     */
    protected $pid = null;

    /**
     * UID владельца дочернего процесса.
     *
     * @var integer
     */
    protected $puid = null;

    /**
     * GUID владельца дочернего процесса.
     *
     * @var integer
     */
    protected $guid = null;

    /**
     * @var null
     */
    protected $masterPid = null;

    /**
     * @var null|string
     */
    protected $pidFileName = 'daemon';

    /**
     * @var null|string
     */
    protected $pidFile = null;

    /**
     * Список PID потомков
     *
     * @var array
     */
    protected $childPids = [];

    #################################
    ##      Logging
    #################################
    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @var Logger
     */
    protected static $defaultLogger = null;

    /**
     * Имя файла для логирования
     *
     * @var null|string
     */
    protected $logFile = null;

    /**
     * @var null|string
     */
    protected $processPath = null;

    /**
     * @var null|string
     */
    protected $logPath = null;

    /**
     * Уникальное имя потока
     * @var null|string
     */
    protected $name = null;

    /**
     * Имя процесса в списке процессов (top)
     *
     * Требуется: http://www.php.net/manual/ru/book.proctitle.php
     * @var null|string
     */
    protected $processTitle = null;

    ##############################################
    ##      Shared memory
    ##############################################
    /**
     * @var bool
     */
    protected $useIpc = false;

    /**
     * @var bool|null
     */
    protected $ipcIsOkay = null;

    /**
     * @var null|string
     */
    protected $ipcPath = null;

    /**
     * Структура для хранения данных для межсетевого взаимодействия процессов.
     *
     * @var array
     */
    protected $internalIpcData = [];

    /**
     * @var null|string
     */
    protected $ipcSegFile = null;

    /**
     * @var null|int
     */
    protected $internalIpcKey = null;

    /**
     * @var null|string
     */
    protected $ipcSemFile = null;

    /**
     * @var null|int
     */
    protected $internalSemKey = null;

    /**
     * Список доступных сигналов и методов их обработки
     *
     * @var array
     */
    protected $signals = [];

    /**
     * Признак принудительно запуска с одной итераций демона
     * @var bool
     */
    protected $isDebug = false;

    /**
     * Флаг для цикла следящего процесса. Цикл работает до завершения процесса.
     *
     * @var boolean
     */
    protected $stopMonitoring = false;

    public function __construct(array $params = null)
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            throw new Exception\RuntimeException('Cannot run on windows');
        } elseif (!in_array(substr(PHP_SAPI, 0, 3), ['cli', 'cgi'])) {
            throw new Exception\RuntimeException('Can only run on CLI or CGI environment');
        } elseif (!function_exists('shmop_open')) {
            throw new Exception\RuntimeException('shmop_* functions are required');
        } elseif (!function_exists('pcntl_fork')) {
            throw new Exception\RuntimeException('pcntl_* functions are required');
        } elseif (!function_exists('posix_kill')) {
            throw new Exception\RuntimeException('posix_* functions are required');
        }

        $this->params = $params;
        if (isset($params['guid'])) {
            $this->guid =  $params['guid'];
        }
        if (isset($params['guid'])) {
            $this->puid =  $params['puid'];
        }

        $this->name = md5(uniqid(rand(), true));
        if (isset($this->params['logPath'])) {
            $this->setLogPath($this->params['logPath']);
        }

        if (isset($this->params['processPath'])) {
            $this->setProcessPath($this->params['processPath']);
        }

        if ($this->useIpc) {
            if (!array_key_exists('ipcPath', $this->params)) {
                throw new Exception\InvalidArgumentException('Для использования разделяемой памяти нужно указать папку'
                    . ' для хранения временных файлов (ipcPath)');
            }
            $this->setIpcPath($this->params['ipcPath']);
        }

        if ($this->useIpc && $this->createIpcSegment() && $this->createIpcSemaphore()) {
            $this->ipcIsOkay = true;
        } else {
            $this->ipcIsOkay = false;
        }
    }

    /**
     * Create IPC semaphore
     *
     * @return bool
     * @throws Exception\RuntimeException When semaphore can't be created
     */
    protected function createIpcSemaphore()
    {
        if ($this->useIpc) {
            $this->ipcSemFile = $this->getIpcPath(). DIRECTORY_SEPARATOR . rand() . $this->name . '.sem';
            touch($this->ipcSemFile);

            $semKey = ftok($this->ipcSemFile, 't');
            if ($semKey === -1) {
                throw new Exception\RuntimeException('Could not create semaphore');
            }

            $this->internalSemKey = @shmop_open($semKey, 'c', 0644, 10);

            if (!$this->internalSemKey) {
                @unlink($this->ipcSemFile);
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception\RuntimeException When segment can't be created
     */
    protected function createIpcSegment()
    {
        if ($this->useIpc) {
            $this->ipcSegFile = $this->getIpcPath() . DIRECTORY_SEPARATOR . rand() . $this->name . '.shm';
            touch($this->ipcSegFile);

            $shmKey = ftok($this->ipcSegFile, 't');
            if ($shmKey === -1) {
                throw new Exception\RuntimeException('Could not create SHM segment');
            }
            $this->internalIpcKey = @shmop_open($shmKey, 'c', 0644, 10240);

            if (!$this->internalIpcKey) {
                @unlink($this->ipcSegFile);
                return false;
            }
        }
        return true;
    }

    /**
     * Read data from IPC segment
     *
     * @throws Exception\RuntimeException When writing of SHM segment fails
     * @return void
     */
    protected function readFromIpcSegment()
    {
        $serializedIpcData = shmop_read($this->internalIpcKey,
            0,
            shmop_size($this->internalIpcKey));

        if ($serializedIpcData === false) {
            throw new Exception\RuntimeException('Fatal error while reading SHM segment');
        }

        $data = @unserialize($serializedIpcData);

        if ($data !== false) {
            $this->internalIpcData = $data;
        }
    }

    /**
     * Write data to IPC segment
     *
     * @throws Exception\RuntimeException When writing of SHM segment fails
     * @return void
     */
    protected function writeToIpcSegment()
    {
        // Read the transaction bit (2 bit of _internalSemKey segment). If it's
        // value is 1, we're into the execution of a PHP_FORK_RETURN_METHOD, so
        // we must not write to segment (data corruption)
        if (shmop_read($this->internalSemKey, 1, 1) === 1) {
            return;
        }

        $serializedIpcData = serialize($this->internalIpcData);

        // Set the exchange array (IPC) into the shared segment
        $shmBytesWritten = shmop_write($this->internalIpcKey,
            $serializedIpcData,
            0);

        // Check if length of SHM segment is enough to contain data
        if ($shmBytesWritten !== strlen($serializedIpcData)) {
            throw new Exception\RuntimeException('Fatal error while writing to SHM segment');
        }
    }

    /**
     * Удаляет общую память и семафоры
     *
     * @return void
     */
    protected function cleanProcessContext()
    {
        if ($this->useIpc) {
            $this->log("Очистка памяти: \n%s\n%s\n%s\n%s",
                $this->internalIpcKey, $this->internalSemKey,
                $this->ipcSegFile, $this->ipcSemFile);
            shmop_delete($this->internalIpcKey);
            shmop_delete($this->internalSemKey);

            shmop_close($this->internalIpcKey);
            shmop_close($this->internalSemKey);

            @unlink($this->ipcSegFile);
            @unlink($this->ipcSemFile);
        }
        $this->isRunning = false;
    }

    /**
     * Удаляет pid из массива всех pid'ов.
     *
     * @param integer $pid
     * @return void
     */
    protected function unsetChildPid($pid)
    {
        $key = array_search($pid, $this->childPids);
        if ($key !== false) {
            unset($this->childPids[$key]);
        }
    }

    /**
     * Удаляет pid-файл текущего процесса.
     *
     * @return void
     */
    protected function unlinkMyPidFile()
    {
        $pidFile = $this->getPidFile();
        if (file_exists($pidFile)) {
            unlink($pidFile);
        }
    }

    /**
     * Возвращает путь до pid-файла текущего процесса.
     *
     * @return string
     */
    public function getPidFile()
    {
        if (is_null($this->pidFile)) {
            if (is_null($this->getProcessPath())) {
                throw new Exception\InvalidArgumentException('Не указана папка для лог-файлов (processPath)');
            }
            $path = rtrim($this->getProcessPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $pid = ($this->pidFileName === null || $this->isChild) ? $this->getPid() : $this->pidFileName;

            // добавляем префикс родителя
            if ($this->isChild) {
                if (!file_exists($path . $this->masterPid)) {
                    if (!mkdir($path . $this->masterPid, 0775, true) && !is_dir($path . $this->masterPid)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $path . $this->masterPid));
                    }
                    @chmod($path . $this->masterPid, 0775);
                }
                $pid = $this->masterPid . DIRECTORY_SEPARATOR . $pid;
            }
            $this->pidFile = $path . $pid . '.pid';
        }
        return $this->pidFile;
    }

    /**
     * Устанавливает путь до pid-файла текущего процесса
     *
     * @param string $pidFile
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = $pidFile;
    }

    /**
     * Возвращает путь до pid-файла указанного процесса.
     *
     * @param int $pid        PID
     * @return string
     */
    protected function buildPidFileByPid($pid)
    {
        if ($this->getProcessPath() === null) {
            throw new Exception\InvalidArgumentException('Не указана папка для лог-файлов (processPath)');
        }
        return rtrim($this->getProcessPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . $this->getPid() . DIRECTORY_SEPARATOR
            . $pid . '.pid';
    }

    /**
     * Принудительная остановка процесса ребенка
     *
     * @param int $pid        PID процесса ребенка
     * @return void
     */
    protected function killChild($pid)
    {
        if ($pid > 0) {
            $status = 0;
            posix_kill($pid, 9);
            pcntl_waitpid($pid, $status, WNOHANG);
            $success = pcntl_wifexited($status);

            $this->unsetChildPid($pid);

            // удаляем PID файл
            $pidFile = $this->buildPidFileByPid($pid);
            if (file_exists($pidFile)) {
                unlink($pidFile);
            }
            $this->log("Принудительное завершение процесса: %s SUCCESS: %s STATUS: %s", $pid, $success, $status);
        }
    }

    /**
     * Устанавливаем заголовок процесса для менеждера процессов в ОС
     *
     * @param string $title
     * @return void
     */
    protected function setProcTitle($title)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($title);
        }
    }

    /**
     * Возвращает имя процесса для менеджера процессов в ОС.
     *
     * @return string
     */
    protected function getProcessTitle()
    {
        return $this->processTitle === null ? $_SERVER['argv'][0] : $this->processTitle;
    }

    /**
     * @param $title
     * @return mixed
     */
    public function setProcessTitle($title)
    {
        return $this->processTitle = $title;
    }

    /**
     * Делает отметку в pid-файле с временем и комментарием
     *
     * @param string $comment
     * @param integer|bool $pidFile
     * @return void
     */
    protected function updateProcessState($comment = '', $pidFile = false)
    {
        $pidFile = $pidFile === false ? $this->getPidFile() : $pidFile;
        if ($pidFile !== false && !file_exists(dirname($pidFile))) {
            if (!mkdir(dirname($pidFile), 0775, true) && !is_dir(dirname($pidFile))) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', dirname($pidFile)));
            }
            @chmod(dirname($pidFile), 0775);
        }

        file_put_contents($pidFile, sprintf("%s\n%s\n%s", microtime(true), $this->getPid(), $comment));
    }

    /**
     * Процедура завершения процесса
     *
     * @return void
     */
    protected function terminateProcess()
    {
        $this->needStop = true;
        $this->unlinkMyPidFile();

        if (!$this->isChild) {
            @rmdir(preg_replace('/\.pid$/', '', dirname($this->getPidFile()) . DIRECTORY_SEPARATOR
                . $this->getPid()));
        }
        $this->log("Завершение процесса");
    }

    /**
     *
     */
    protected function preForkChild()
    {
    }

    /**
     * Создает потомка текущего процесса
     *
     * @return void
     */
    protected function singleFork()
    {
        // инициализация до запуска ребенка
        $this->preForkChild();

        @ob_end_flush();

        pcntl_signal(SIGCHLD, SIG_IGN);
        $pid = @pcntl_fork();
        if ($pid === -1) {
            throw new Exception\RuntimeException('Forking process failed');
        } elseif ($pid === 0) {
            $this->pidFile = null;
            // This is the child
            $this->isChild = true;
            $this->setPid(posix_getpid());

            $application = basename(getcwd());
            $this->setProcTitle($this->getProcessTitle() . ":[{$application}] worker");
            $this->updateProcessState('init');

            // Sleep a second to avoid problems
            sleep(1);

            // Install the signal handler
            pcntl_signal(SIGTERM, [$this, 'sigHandler']);
            pcntl_signal(SIGHUP, [$this, 'sigHandler']);
            pcntl_signal(SIGUSR1, [$this, 'sigHandler']);

            foreach ($this->signals as $signal => $method_name) {
                if (method_exists($this, $method_name)) {
                    pcntl_signal($signal, [$this, $method_name]);
                }
            }

            // If requested, change process identity
            if ($this->guid !== null) {
                posix_setgid($this->guid);
            }

            if ($this->puid !== null) {
                posix_setuid($this->puid);
            }

            // Run the child
            try {
                $this->run();
                $this->log("RUN END");
            } catch (\Exception $e) {
                // We have to catch any exceptions and clean up the process,
                // else we will have a memory leak.
            }

            // Destroy the child after _run() execution. Required to avoid
            // unused child processes after execution
            exit(0);
        } else {
            // Else this is the parent
            $this->isChild   = false;
            $this->isRunning = true;

            // создаем PID-файл перед тем как добавить идентификатор в список
            $childPidFile = $this->buildPidFileByPid($pid);
            $this->updateProcessState('preinit', $childPidFile);

            $this->childPids[] = $pid;
        } // if
    }

    /**
     * Возвращает время последней записи в pid-файл или FALSE в противном случае
     * (читает первую строку файла).
     *
     * @param string $pidFile            имя pid-файла
     * @return mixed
     */
    protected function checkPidFile($pidFile)
    {
        $attempt = 0;                     // попытки прочитать pid-файл
        $line = '';                       // строка прочитанная из pid-файла

        while ($attempt < $this->maxSuspiciousAttempts && $line == '') {
            if (file_exists($pidFile)) {
                $file = fopen($pidFile, 'r');
                if (!$file) {
                    continue;
                }
                // читать файл полнотью не нужно...
                $line = fgets($file);         // ... читаем первую строку
                if (!fgets($file)) {          // если мы смогли прочитать что-то из 2-й строки,
                    $line = '';               // значит первую строку прочитали полностью
                }
                fclose($file);
            }
            if ($attempt > 0) {
                $this->log('checkPidFile attempt %s', $attempt);
            }
            $attempt++;
        }
        return ($attempt > $this->maxSuspiciousAttempts) ? false : $line;
    }

    /**
     * Удаляет из массива pid-файлов детей (childPids) тех, чьи файлы уже удалены
     *
     * @return void
     */
    protected function waitStopingChilds()
    {
        // начинаем ждать завершения детей
        $mtime = microtime(true);

        while (count($this->childPids) > 0 && $mtime + $this->timeoutTERM/1000 > microtime(true)) {
            foreach ($this->childPids as $pid) {
                if ($pid > 0) {
                    // файл с PID процесса ребенка
                    $childPidFile = $this->buildPidFileByPid($pid);

                    // если файла нет, удаляем PID из списка детей
                    if (!file_exists($childPidFile)) {
                        $this->unsetChildPid($pid);
                    }
                } // if
            }
        } // while
    }

    /**
     * Перезапускает процесс ребенка
     *
     * @param integer $childPid
     * @return void
     */
    protected function restartChild($childPid)
    {
        // посылаем сигнал, чтобы ребенок умер
        if ($childPid > 0) {
            posix_kill($childPid, SIGTERM);

            // ждем заверщения процесса
            $this->waitStopingChilds();

            // если процесс еще в списке, убиваем его
            $key = array_search($childPid, $this->childPids);
            if ($key !== false) {
                $this->killChild($childPid);
            }

            // запускаем новый дочерний процесс
            if (!$this->stop) {
                $this->singleFork();
            }
        }
    }

    /**
     * Алгоритм мониторинга родителем своих потомков (одна итерация)
     *
     * @return void
     */
    protected function monitoringLoop()
    {
        // время бездействия потомка
        $maxIdle = $this->processIdle / 1000;

        foreach ($this->childPids as $pid) {
            $pidFile = $this->buildPidFileByPid($pid);
            $toRestart = false;
            $reason = 'Причина неизвестна';      // причина перезапуска процесса для лога

            // проверка по PID-файлу
            if (!file_exists($pidFile)) {
                $toRestart = true;
                $reason = 'Отсутствует pid-файл';
            } else {
                $time = $this->checkPidFile($pidFile);
                if ($time === false) {
                    $toRestart = true;
                    $reason = sprintf('Достигнуто максимально число подозрительных проверок.');
                }

                if ($time !== false && $maxIdle != 0 && ($time + $maxIdle < microtime(true))) {
                    $toRestart = true;
                    $reason = sprintf('Прошло более %s сек. с последнего изменения', $maxIdle);
                }

                // отключили проверку по таймауту, тогда проверяем существование процесса в ОС
                if ($maxIdle == 0) {
                    $fail = true;
                    $cmdLineFile = "/proc/{$pid}/cmdline"; // файл должен содержать имя процеса (procTitle)
                    if (file_exists('/proc/' . $pid) && file_exists($cmdLineFile)) {
                        $hnd = fopen($cmdLineFile, 'r');
                        // процесс существует в ОС и это процесс с нашим именен
                        $fail = (stripos(fgets($hnd), $this->getProcessTitle()) === false);
                    }
                    if ($fail) {
                        $toRestart = true;
                        $reason = sprintf('В ОС отсутсвует процес');
                        // удаляем этот PID из списка, чтобы не убить чужой процесс
                        $this->unsetChildPid($pid);
                        if (file_exists($pidFile)) {
                            unlink($pidFile);
                        }
                    }
                }
                // перезапуск при привышении лимина используемой памяти
                if (!is_null($this->memoryLimit)) {
                    exec('ps -o rss -p ' . $pid, $output);
                    //rss is given in 1024 byte units
                    if (isset($output[1])) {
                        $memoryUsage = $output[1];
                        $toRestart = (int)$memoryUsage > (int)$this->memoryLimit;
                        if ($toRestart) {
                            $reason = sprintf('Превышен разрешенный предел выделенной памяти %s(%s) КБ.', $memoryUsage, $this->memoryLimit);
                        }
                    }
                }
            } // if

            if ($toRestart) {
                $this->warn("Перезапуск потомка (%s). %s", $pid, $reason);
                $this->restartChild($pid);
                $this->warn("Перезапуск потомка завершен (%s)", $pid);
            }
        }// foreach
    }

    /**
     * Стандартный метод мониторинга процессов детей
     *
     * @return void
     */
    protected function monitoring()
    {
        while (!$this->stopMonitoring) {
            // востанавливаем детей, если идет работа
            if (!$this->stop) {
                $this->monitoringLoop();
            }
            // обновляем статус процесса
            $this->updateProcessState('_monitoring');

            // засыпаем
            usleep($this->monitoringSleep * 1000);
        }
        $this->terminateProcess();
    }



    /**
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * Реализация одного цикла демона
     */
    abstract public function cycle();

    /**
     * Initialization before starting
     */
    public function init()
    {
    }

    protected function initMaster()
    {
    }

    /**
     * Loop shutdown functions
     */
    public function shutdown()
    {
    }

    public function start()
    {
        if (file_exists($this->getPidFile())) {
            throw new Exception\RuntimeException("Процесс с pid-файлом {$this->getPidFile()} уже запущен");
        }
        if ($this->useIpc && !$this->ipcIsOkay) {
            throw new Exception\RuntimeException('Unable to create SHM segments for process communications');
        }

        $this->log('Запущен процесс с pid-файлом: %s', $this->getPidFile());
        // начало работы процесса
        $this->stop = false;

        // @see http://www.php.net/manual/en/function.pcntl-fork.php#41150
        @ob_end_flush();

        // инициализация до запуска следящего родителя
        $this->initMaster();

        $this->isDebug = file_exists($this->getProcessPath() . DIRECTORY_SEPARATOR. '.debug');
        //pcntl_signal(SIGCHLD, SIG_IGN);
        $pid = @pcntl_fork();
        if ($pid === -1) {
            throw new Exception\RuntimeException('Forking process failed');
        } elseif ($pid === 0) {
            $this->setPid(posix_getpid());
            $application = basename(getcwd());
            $this->setProcTitle($this->getProcessTitle() . ":[{$application}] master");
            $this->masterPid = $this->getPid();
            $this->updateProcessState('init');

            // Следящий родитель
            for ($c = 0; $c < $this->childNumber; $c++) {
                $this->singleFork();
            } // for

            if ($this->isDebug) {
                $this->getLogger()->info('[DEBUG][MASTER] Master exit.', ['pid' => $this->getPid()]);
                echo "\n[DEBUG] Master exit.\n";

                $this->stopMonitoring = true;
                $this->terminateProcess();
                return;
            }

            pcntl_signal(SIGTERM, [$this, 'sigHandler']);
            pcntl_signal(SIGHUP, [$this, 'sigHandler']);

            $this->monitoring();
        }// основной родитель
    }


    /**
     * @return bool
     */
    public function stop()
    {
        if ($this->isRunning) {
            // Вызов из основного процесса
            $this->stop = true;

            // отправляем детям сигнал на завершение...
            foreach ($this->childPids as $pid) {
                $this->log("Send SIGTERM to child %s", $pid);
                if ($pid > 0) {
                    posix_kill($pid, SIGTERM);
                }
            }

            // ожидаем завершение процессов детей
            $this->waitStopingChilds();

            // ...прошло больше положенного времени
            // отправляем детям сигнал на смерть...
            if (count($this->childPids) > 0) {
                $this->kill();
            }
            $this->cleanProcessContext();
            $this->log("Остановка процесса завершена");
        } else {
            $this->log("Try remote termination (from PID %s)", posix_getpid());
            $pidFile = $this->getPidFile();
            // вызов из стороннего процесса (где был создан объект класса демона)
            if ($pid = $this->getPidFromFile()) {
                // убиваем по-хорошему...
                posix_kill($pid, SIGTERM);
                sleep(3); // ждем

                $attempt = 1;
                $this->log('Check pid file %s (from PID %s)', $pidFile, posix_getpid());
                while (file_exists($pidFile) && $attempt <= $this->attemptToKillChild) {
                    $this->log("Remote terminate (from PID %s)", posix_getpid());
                    $attempt++;
                    sleep(1);
                }

                $this->log("Was attempted %s from %s", $attempt, $this->attemptToKillChild);
                if (file_exists($pidFile)) { // теперь убиваем по-плохому...
                    $this->warn("Try to force remote termination (from PID %s)", posix_getpid());
                    posix_kill($pid, SIGKILL);
                    pcntl_waitpid($pid, $status, WNOHANG);
                    pcntl_wifexited($status);
                    $this->warn("Termination done (from PID %s)", posix_getpid());
                    $this->warn("Try to remove pid file %s", $pidFile);
                    unlink($pidFile);
                    $this->warn((file_exists($pidFile) ? "Unable to delete file %s" : "File %s was deleted successfully"),
                        $pidFile);
                } else {
                    $this->log("Remote terminated successfully (from PID %s)", posix_getpid());
                }
            } else {
                $this->log("PID file not found (from PID %s)", posix_getpid());
            }
        }

        return true;
    }

    /**
     * Daemon run
     */
    public function run()
    {
        $this->log('Запуск демона');
        $this->init();
        $this->startTime = time();
        $this->isDebug = file_exists($this->getProcessPath() . DIRECTORY_SEPARATOR. '.debug');

        while (!$this->needStop) {
            try {
                $this->cycle();
                if ($this->isDebug) {
                    $this->getLogger()->info('[DEBUG][MASTER] Child exit.', ['pid' => $this->getPid()]);
                    echo "\n[DEBUG] Child exit.";
                    break;
                }
                $this->updateProcessState('Завершилась итерация ' . $this->cycleCount);

                // Завершение работы демона по кол-ву циклов
                if ($this->cyclesLimit !== null && $this->cycleCount >= $this->cyclesLimit) {
                    break;
                }
                // Интервал между циклами
                if ($this->interval !== null) {
                    usleep($this->interval);
                }
                $this->cycleCount++;
            } catch (\Exception $e) {
                $trace = $this->getFullTrace($e);
                $this->err($trace);
                $this->terminateProcess();

                // Debug trace
                if ($this->isDebug) {
                    $banner = sprintf("DEBUG MODE: %s (%s)", $this->processTitle, get_class($this));
                    $border = sprintf("\n==%s==", str_repeat('-', 8 + strlen($banner)));
                    echo  $border . "\n==--- {$banner} ---==" . $border . "\n"
                        . $trace;
                }
            }
        }

        $this->shutdown();
        $this->terminateProcess();
        exit;
    }

    /**
     * @param \Exception $e
     * @return mixed
     */
    protected function getFullTrace($e)
    {
        $trace = '';
        do {
            $posix = new Posix();
            $trace .= sprintf(
                "\n%s\n%s\n%s\n\n",
                str_repeat("=", $posix->getWidth()),
                $e->getMessage(),
                $e->getTraceAsString()
            );
            $e = $e->getPrevious();
        } while ($e);
        return $trace;
    }

    /**
     * Обработчик сигнала,  который делает возможной связь между клиентом и сервером.
     *
     * @param  integer $signal
     * @return void
     */
    public function sigHandler($signal)
    {
        $this->log("SIGNAL RECEIVED: " . $signal);

        switch ($signal) {
            case SIGTERM:
            case SIGHUP:
                $this->log("SIG: " . $signal);
                // посылаем сигнал TERM всем детям
                if (!$this->isChild) {
                    $this->stopMonitoring = true;
                    $this->stop();
                } else {
                    $this->terminateProcess();
                    exit;
                }
                break;

            case SIGUSR1:
                if (!$this->useIpc) {
                    break;
                }
                // This is the User-defined signal we'll use. Read the SHM segment
                $this->readFromIpcSegment();

                if (isset($this->internalIpcData['_callType']) && !empty($this->internalIpcData['_callMethod'])) {
                    $method = $this->internalIpcData['_callMethod'];
                    $params = isset($this->internalIpcData['_callInput']) ? $this->internalIpcData['_callInput'] : null;

                    switch ($this->internalIpcData['_callType']) {
                        case self::VOID_METHOD:
                            // Simple call the (void) method and return immediately
                            // no semaphore is placed into parent, so the processing
                            // is async
                            call_user_func([$this, $method], $params);
                            break;

                        case self::RETURN_METHOD:
                            // Process the request
                            $this->internalIpcData['_callOutput'] = call_user_func([$this, $method], $params);

                            // Write the result into IPC segment
                            $this->writeToIPCsegment();

                            // Unlock the semaphore but block _writeToIpcSegment()
                            shmop_write($this->internalSemKey, 0, 0);
                            shmop_write($this->internalSemKey, 1, 1);
                            break;
                    }
                }
                break;

            default:
                // Ignore all other signals
                break;
        }
    }

    /**
     * Установка интервала между циклами (в секундах)
     *
     * @param integer $interval
     */
    public function setIntervalSecond($interval = null)
    {
        if ($interval === null) {
            $this->interval = null;
            return;
        }

        $this->interval = (int)$interval * 1000000;
    }

    /**
     * Установка ограничения по кол-ву циклов
     * @param integer $limit
     */
    public function setCyclesLimit($limit)
    {
        $this->cyclesLimit = (int)$limit;
    }

    /**
     * Установка ограничения по времени работы демона
     *
     * @param integer $limit
     */
    public function setTimeLimit($limit)
    {
        $this->timeLimit = (int)$limit;
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
     * Возвращает ресурс лог-файла текущего процесса.
     *
     * @return resource
     */
    public function getLogFile()
    {
        if (is_null($this->logFile)) {
            if (is_null($this->getLogPath())) {
                throw new Exception\InvalidArgumentException('Не указана папка для лог-файлов (logPath)');
            }
            $pid = ($this->pidFileName === null || $this->isChild) ? $this->getPid() : $this->pidFileName;
            if ($this->isChild) {
                $pid = $this->masterPid . DIRECTORY_SEPARATOR . $pid;
            }
            $this->logFile = rtrim($this->getLogPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                . $pid . '.log';

            if (!file_exists(dirname($this->logFile))) {
                if (!mkdir(dirname($this->logFile), 0775, true) && !is_dir(dirname($this->logFile))) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', dirname($this->logFile)));
                }
                @chmod(dirname($this->logFile), 0775);
            }
            file_put_contents($this->logFile, '', FILE_APPEND);
            @chmod($this->logFile, 0766);
        }

        return $this->logFile;
    }

    /**
     * @param int|null $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Возвращает PID процесса из pid файла
     *
     * @return null|int
     */
    public function getPidFromFile()
    {
        $pidFile = $this->getPidFile();
        $pid = null;
        if (file_exists($pidFile)) {
            do {
                $lines = file($pidFile);
            } while (count($lines) < 3);

            $pid = trim($lines[1]);
        }
        return $pid;
    }

    protected function getLogMessage()
    {
        $message = (!$this->isChild ? ' [MASTER]': '');
        $args = func_get_args();
        if (count($args) == 1) {
            $message = $args[0];
        } elseif (count($args) > 1) {
            $message = call_user_func_array('sprintf', $args);
        }
        return $message;
    }

    /**
     * Добавляет запись типа INFO в лог-файл
     *
     * $this->log("Init child #3");
     * $this->log("Init %s #%d", 'child', 3);
     */
    public function log()
    {
        $mes = call_user_func_array([$this, 'getLogMessage'], func_get_args());
        $this->getLogger()->info($mes, ['pid' => $this->getPid()]);
    }

    /**
     * Добавляет запись типа WARNING в лог-файл (info)
     *
     * $this->warn("Init child #3");
     * $this->warn("Init %s #%d", 'child', 3);
     */
    public function warn()
    {
        $mes = call_user_func_array([$this, 'getLogMessage'], func_get_args());
        $this->getLogger()->warn($mes, ['pid' => $this->getPid()]);
    }

    /**
     * Добавляет запись типа ERROR в лог-файл (info)
     *
     * $this->err("Init child #3");
     * $this->err("Init %s #%d", 'child', 3);
     */
    public function err()
    {
        $mes = call_user_func_array([$this, 'getLogMessage'], func_get_args());
        $this->getLogger()->err($mes, ['pid' => $this->getPid()]);
    }

    /**
     * Осуществляет принудительную остановку всех процессов-детей.
     *
     * @return void
     */
    public function kill()
    {
        foreach ($this->childPids as $pid) {
            $this->warn("Попытка принудительной остановки процесса %s", $pid);
            $this->killChild($pid);
        }
        $this->warn("Принудительная остановка завершена %s", count($this->childPids));
    }

    /**
     * @param null|string $processPath
     * @return $this
     */
    public function setProcessPath($processPath)
    {
        $this->processPath = $processPath;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getProcessPath()
    {
        return $this->processPath;
    }

    /**
     * @param null|string $logPath
     * @return $this
     */
    public function setLogPath($logPath)
    {
        $this->logPath = $logPath;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLogPath()
    {
        return $this->logPath;
    }

    /**
     * @param null|string $ipcPath
     * @return $this
     */
    public function setIpcPath($ipcPath)
    {
        $this->ipcPath = rtrim($ipcPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . get_class($this). DIRECTORY_SEPARATOR;
        if (!file_exists($this->ipcPath)) {
            if (!mkdir($this->ipcPath, 0775, true) && !is_dir($this->ipcPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->ipcPath));
            }
        }
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIpcPath()
    {
        return $this->ipcPath;
    }

    /**
     * @param \Zend\Log\Logger $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        if (is_null($this->logger)) {
            $logger = new Logger();
            $writer = new Stream($this->getLogFile());
            $writer->setFormatter(new Sprintf("[%timestamp% | %6s:pid%] %priorityName% %message%"));
            $logger->addWriter($writer);
            $this->setLogger($logger);
            $defaultLogger = self::getDefaultLogger();
            if (empty($defaultLogger)) {
                self::setDefaultLogger($logger);
            }
        }

        return $this->logger;
    }

    /**
     * @param \Zend\Log\Logger $defaultLogger
     */
    public static function setDefaultLogger($defaultLogger)
    {
        self::$defaultLogger = $defaultLogger;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public static function getDefaultLogger()
    {
        return self::$defaultLogger;
    }
}
